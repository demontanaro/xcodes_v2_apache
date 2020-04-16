<?php
$rRelease = 21;             // Official Beta Release Number
$rEarlyAccess = "";	    	// Early Access Release
$rTimeout = 60;             // Seconds Timeout for Queries, Functions & Requests
$rDebug = False;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($rDebug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

set_time_limit($rTimeout);
ini_set('mysql.connect_timeout', $rTimeout);
ini_set('max_execution_time', $rTimeout);
ini_set('default_socket_timeout', $rTimeout);

define("MAIN_DIR", "/home/xtreamcodes/iptv_xtream_codes/");
define("CONFIG_CRYPT_KEY", "5709650b0d7806074842c6de575025b1");

require_once realpath(dirname(__FILE__))."/mobiledetect.php";
require_once realpath(dirname(__FILE__))."/gauth.php";

$detect = new Mobile_Detect;
$rStatusArray = Array(0 => "Stopped", 1 => "Running", 2 => "Starting", 3 => "<strong style='color:#cc9999'>DOWN</strong>", 4 => "On Demand", 5 => "Direct");
$rClientFilters = Array(
    "NOT_IN_BOUQUET" => "Not in Bouquet",
    "CON_SVP" => "Connection Issue",
    "ISP_LOCK_FAILED" => "ISP Lock Failed",
    "USER_DISALLOW_EXT" => "Extension Disallowed",
    "AUTH_FAILED" => "Authentication Failed",
    "USER_EXPIRED" => "User Expired",
    "USER_DISABLED" => "User Disabled",
    "USER_BAN" => "User Banned"
);

$rResetSettings = Array("allowed_stb_types" => '["AuraHD","AuraHD2","AuraHD3","AuraHD4","AuraHD5","AuraHD6","AuraHD7","AuraHD8","AuraHD9","MAG200","MAG245","MAG245D","MAG250","MAG254","MAG255","MAG256","MAG257","MAG260","MAG270","MAG275","MAG322","MAG323","MAG324","MAG325","MAG349","MAG350","MAG351","MAG352","MAG420","WR320"]', "client_prebuffer" => 20, "mag_container" => "ts", "probesize" => 5000000, "stream_max_analyze" => 5000000, "user_auto_kick_hours" => 0, "disallow_empty_user_agents" => 0, "show_all_category_mag" => 1, "flood_limit" => 120, "stream_start_delay" => 0, "vod_bitrate_plus" => 200, "read_buffer_size" => 8192, "tv_channel_default_aspect" => 0, "playback_limit" => 3, "show_tv_channel_logo" => 1, "show_channel_logo_in_preview" => 1, "enable_connection_problem_indication" => 1, "vod_limit_at" => 20, "persistent_connections" => 1, "record_max_length" => 180, "max_local_recordings" => 10, "allowed_stb_types_for_local_recording" => '["MAG255","MAG256","MAG257"]', "stalker_theme" => "emerald", "rtmp_random" => 1, "use_buffer" => 0, "restreamer_prebuffer" => 0, "audio_restart_loss" => 0, "channel_number_type" => "bouquet", "stb_change_pass" => 1, "enable_debug_stalker" => 0, "online_capacity_interval" => 10, "always_enabled_subtitles" => 1, "save_closed_connection" => 1, "client_logs_save" => 1, "case_sensitive_line" => 1, "county_override_1st" => 0, "disallow_2nd_ip_con" => 0, "firewall" => 0, "use_mdomain_in_lists" => 0, "priority_backup" => 0, "series_custom_name" => 0, "mobile_apps" => 0, "mag_security" => 1);

function APIRequest($rData) {
    global $rAdminSettings, $rServers, $_INFO;
    ini_set('default_socket_timeout', 5);
    if ($rAdminSettings["local_api"]) {
        $rAPI = "http://127.0.0.1:".$rServers[$_INFO["server_id"]]["http_broadcast_port"]."/api.php";
    } else {
        $rAPI = "http://".$rServers[$_INFO["server_id"]]["server_ip"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"]."/api.php";
    }
    $rPost = http_build_query($rData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rAPI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $rData = curl_exec($ch);
    return $rData;
}

function SystemAPIRequest($rServerID, $rData) {
    global $rServers, $rSettings;
    ini_set('default_socket_timeout', 5);
    $rAPI = "http://".$rServers[intval($rServerID)]["server_ip"].":".$rServers[intval($rServerID)]["http_broadcast_port"]."/system_api.php";
    $rData["password"] = $rSettings["live_streaming_pass"];
    $rPost = http_build_query($rData);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rAPI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rPost);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $rData = curl_exec($ch);
    return $rData;
}

function sexec($rServerID, $rCommand) {
    global $_INFO;
    if ($rServerID <> $_INFO["server_id"]) {
        return SystemAPIRequest($rServerID, Array("action" => "BackgroundCLI", "cmds" => Array($rCommand)));
    } else {
        return exec($rCommand);
    }
}

function getPIDs($rServerID) {
    global $rAdminSettings;
    $rReturn = Array();
    $rFilename = tempnam(MAIN_DIR.'tmp/', 'proc_');
    $rCommand = "ps aux >> ".$rFilename;
    sexec($rServerID, $rCommand);
    $rData = ""; $rI = 3;
    while (strlen($rData) == 0) {
        $rData = SystemAPIRequest($rServerID, Array('action' => 'getFile', 'filename' => $rFilename));
        $rI --;
        if (($rI == 0) OR (strlen($rData) > 0)) { break; }
        sleep(1);
    }
    $rProcesses = explode("\n", $rData);
    array_shift($rProcesses);
    foreach ($rProcesses as $rProcess) {
        $rSplit = explode(" ", preg_replace('!\s+!', ' ', trim($rProcess)));
        if (strlen($rSplit[0]) > 0) {
            $rReturn[] = Array("user" => $rSplit[0], "pid" => $rSplit[1], "cpu" => $rSplit[2], "mem" => $rSplit[3], "vsz" => $rSplit[4], "rss" => $rSplit[5], "tty" => $rSplit[6], "stat" => $rSplit[7], "start" => $rSplit[8], "time" => $rSplit[9], "command" => join(" ", array_splice($rSplit, 10, count($rSplit)-10)));
        }
    }
    return $rReturn;
}

function getFreeSpace($rServerID) {
    $rReturn = Array();
    $rFilename = tempnam(MAIN_DIR.'tmp/', 'fs_');
    $rCommand = "df -h >> ".$rFilename;
    sexec($rServerID, $rCommand);
    $rData = SystemAPIRequest($rServerID, Array('action' => 'getFile', 'filename' => $rFilename));
    $rLines = explode("\n", $rData);
    array_shift($rLines);
    foreach ($rLines as $rLine) {
        $rSplit = explode(" ", preg_replace('!\s+!', ' ', trim($rLine)));
        if ((strlen($rSplit[0]) > 0) && (strpos($rSplit[5], "xtreamcodes") !== false)) {
            $rReturn[] = Array("filesystem" => $rSplit[0], "size" => $rSplit[1], "used" => $rSplit[2], "avail" => $rSplit[3], "percentage" => $rSplit[4], "mount" => join(" ", array_slice($rSplit, 5, count($rSplit)-5)));
        }
    }
    return $rReturn;
}

function remoteCMD($rServerID, $rCommand) {
    $rReturn = Array();
    $rFilename = tempnam(MAIN_DIR.'tmp/', 'cmd_');
    sexec($rServerID, $rCommand." >> ".$rFilename);
	$rData = ""; $rI = 3;
    while (strlen($rData) == 0) {
        $rData = SystemAPIRequest($rServerID, Array('action' => 'getFile', 'filename' => $rFilename));
        $rI --;
        if (($rI == 0) OR (strlen($rData) > 0)) { break; }
        sleep(1);
    }
	unset($rFilename);
    return $rData;
}

function freeTemp($rServerID) {
    sexec($rServerID, "rm ".MAIN_DIR."tmp/*");
}

function freeStreams($rServerID) {
    sexec($rServerID, "rm ".MAIN_DIR."streams/*");
}

function getStreamPIDs($rServerID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `streams`.`type`, `streams_sys`.`pid`, `streams_sys`.`monitor_pid`, `streams_sys`.`delay_pid` FROM `streams_sys` LEFT JOIN `streams` ON `streams`.`id` = `streams_sys`.`stream_id` WHERE `streams_sys`.`server_id` = ".intval($rServerID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            foreach (Array("pid", "monitor_pid", "delay_pid") as $rPIDType) {
                if ($row[$rPIDType]) {
                    $return[$row[$rPIDType]] = Array("id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => $rPIDType);
                }
            }
        }
    }
    $result = $db->query("SELECT `id`, `stream_display_name`, `type`, `tv_archive_pid` FROM `streams` WHERE `tv_archive_server_id` = ".intval($rServerID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ($row["pid"]) {
                $return[$row["pid"]] = Array("id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => "timeshift");
            }
        }
    }
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `streams`.`type`, `user_activity_now`.`pid` FROM `user_activity_now` LEFT JOIN `streams` ON `streams`.`id` = `user_activity_now`.`stream_id` WHERE `user_activity_now`.`server_id` = ".intval($rServerID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ($row["pid"]) {
                $return[$row["pid"]] = Array("id" => $row["id"], "title" => $row["stream_display_name"], "type" => $row["type"], "pid_type" => "activity");
            }
        }
    }
    return $return;
}

function checkSource($rServerID, $rFilename) {
    global $rServers, $rSettings;
    $rAPI = "http://".$rServers[intval($rServerID)]["server_ip"].":".$rServers[intval($rServerID)]["http_broadcast_port"]."/system_api.php?password=".$rSettings["live_streaming_pass"]."&action=getFile&filename=".urlencode($rFilename);
    $rCommand = 'timeout 5 '.MAIN_DIR.'bin/ffprobe -show_streams -v quiet "'.$rAPI.'" -of json';
    return json_decode(shell_exec($rCommand), True);
}

function getSelections($rSources) {
    global $db;
    $return = Array();
    foreach ($rSources as $rSource) {
        $result = $db->query("SELECT `id` FROM `streams` WHERE `type` IN (2,5) AND `stream_source` LIKE '%".$db->real_escape_string(str_replace("/", "\/", $rSource))."\"%' ESCAPE '|' LIMIT 1;");
        if (($result) && ($result->num_rows == 1)) {
            $return[] = intval($result->fetch_assoc()["id"]);
        }
    }
    return $return;
}

function getBackups() {
    $rBackups = Array();
    foreach (scandir(MAIN_DIR."adtools/backups/") as $rBackup) {
        $rInfo = pathinfo(MAIN_DIR."adtools/backups/".$rBackup);
        if ($rInfo["extension"] == "sql") {
            $rBackups[] = Array("filename" => $rBackup, "timestamp" => filemtime(MAIN_DIR."adtools/backups/".$rBackup), "date" => date("Y-m-d H:i:s", filemtime(MAIN_DIR."adtools/backups/".$rBackup)), "filesize" => filesize(MAIN_DIR."adtools/backups/".$rBackup));
        }
    }
    usort($rBackups, function($a, $b) {
        return $a['timestamp'] <=> $b['timestamp'];
    });
    return $rBackups;
}

function parseRelease($rRelease) {
    $rCommand = "/usr/bin/python ".MAIN_DIR."pytools/release.py \"".$rRelease."\"";
    return json_decode(shell_exec($rCommand), True);
}

function listDir($rServerID, $rDirectory, $rAllowed=null) {
    global $rServers, $_INFO, $rSettings;
    set_time_limit(60);
    ini_set('max_execution_time', 60);
	$rReturn = Array("dirs" => Array(), "files" => Array());
    if ($rServerID == $_INFO["server_id"]) {
        $rFiles = scanDir($rDirectory);
        foreach ($rFiles as $rKey => $rValue) {
            if (!in_array($rValue, Array(".",".."))) {
                if (is_dir($rDirectory."/".$rValue)) {
                    $rReturn["dirs"][] = $rValue;
                } else {
                    $rExt = strtolower(pathinfo($rValue)["extension"]);
                    if (((is_array($rAllowed)) && (in_array($rExt, $rAllowed))) OR (!$rAllowed)) {
                        $rReturn["files"][] = $rValue;
                    }
                }
            }
        }
    } else {
        $rData = SystemAPIRequest($rServerID, Array('action' => 'viewDir', 'dir' => $rDirectory));
        $rDocument = new DOMDocument();
        $rDocument->loadHTML($rData);
        $rFiles = $rDocument->getElementsByTagName('li');
        foreach($rFiles as $rFile) {
            if (stripos($rFile->getAttribute('class'), "directory") !== false) {
                $rReturn["dirs"][] = $rFile->nodeValue;
            } else if (stripos($rFile->getAttribute('class'), "file") !== false) {
                $rExt = strtolower(pathinfo($rFile->nodeValue)["extension"]);
                if (((is_array($rAllowed)) && (in_array($rExt, $rAllowed))) OR (!$rAllowed)) {
                    $rReturn["files"][] = $rFile->nodeValue;
                }
            }
        }
    }
    return $rReturn;
}

function scanRecursive($rServerID, $rDirectory, $rAllowed=null) {
    $result = [];
    $rFiles = listDir($rServerID, $rDirectory, $rAllowed);
    foreach ($rFiles["files"] as $rFile) {
        $rFilePath = rtrim($rDirectory, "/").'/'.$rFile;
        $result[] = $rFilePath;
    }
    foreach ($rFiles["dirs"] as $rDir) {
        foreach (scanRecursive($rServerID, rtrim($rDirectory, "/")."/".$rDir."/", $rAllowed) as $rFile) {
            $result[] = $rFile;
        }
    }
    return $result;
}

function getEncodeErrors($rID) {
    global $rSettings;
    $rServers = getStreamingServers(true);
    ini_set('default_socket_timeout', 3);
    $rErrors = Array();
    $rStreamSys = getStreamSys($rID);
    foreach ($rStreamSys as $rServer) {
        $rServerID = $rServer["server_id"];
        if (isset($rServers[$rServerID])) {
            if (!($rServer["pid"] > 0 && $rServer["to_analyze"] == 0 && $rServer["stream_status"] <> 1)) {
                $rFilename = MAIN_DIR."movies/".intval($rID).".errors";
                $rError = SystemAPIRequest($rServerID, Array('action' => 'getFile', 'filename' => $rFilename));
                if (strlen($rError) > 0) {
                    $rErrors[$rServerID] = $rError;
                }
            }
        }
    }
    return $rErrors;
}

function getTimeDifference($rServerID) {
	global $rServers, $rSettings;
    ini_set('default_socket_timeout', 3);
    $rError = SystemAPIRequest($rServerID, Array('action' => 'getDiff', 'main_time' => intval(time())));
    return intval(file_get_contents($rAPI));
}

function deleteMovieFile($rServerID, $rID) {
	global $rServers, $rSettings;
    ini_set('default_socket_timeout', 3);
    $rCommand = "rm ".MAIN_DIR."movies/".$rID.".*";
    return SystemAPIRequest($rServerID, Array('action' => 'BackgroundCLI', 'action' => Array($rCommand)));
}

function generateString($strength = 10) {
    $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

function xor_parse($data, $key) {
    $i = 0;
    $output = '';
    foreach (str_split($data) as $char) {
        $output.= chr(ord($char) ^ ord($key[$i++ % strlen($key)]));
    }
    return $output;
}

function getTimezone() {
    global $db;
    $result = $db->query("SELECT `default_timezone` FROM `settings`;");
    if ((isset($result)) && ($result->num_rows == 1)) {
        return $result->fetch_assoc()["default_timezone"];
    } else {
        return "Europe/London";
    }
}

$_INFO = json_decode(xor_parse(base64_decode(file_get_contents(MAIN_DIR . "config")), CONFIG_CRYPT_KEY), True);
if (!$db = new mysqli($_INFO["host"], $_INFO["db_user"], $_INFO["db_pass"], $_INFO["db_name"], $_INFO["db_port"])) { exit("No MySQL connection!"); } 
$db->set_charset("utf8");
date_default_timezone_set(getTimezone());

function getStreamingServers($rActive = false) {
    global $db, $rPermissions;
    $return = Array();
    if ($rActive) {
        $result = $db->query("SELECT * FROM `streaming_servers` WHERE `status` = 1 ORDER BY `id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `streaming_servers` ORDER BY `id` ASC;");
    }
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($rPermissions["is_reseller"]) {
                $row["server_name"] = "Server #".$row["id"];
            }
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}

function getStreamingServersByID($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `streaming_servers` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getSettings() {
    global $db;
    $result = $db->query("SELECT * FROM `settings` LIMIT 1;");
    return $result->fetch_assoc();
}

function getStreamList() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `streams`.`id`, `streams`.`stream_display_name`, `stream_categories`.`category_name` FROM `streams` LEFT JOIN `stream_categories` ON `stream_categories`.`id` = `streams`.`category_id` ORDER BY `streams`.`stream_display_name` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getStreams($category_id=null, $full=false, $stream_ids=null) {
    global $db;
    $return = Array();
    if ($stream_ids) {
        $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 AND `id` IN (".join(",", $stream_ids).") ORDER BY `id` ASC;");
    } else {
        if ($category_id) {
            $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 AND `category_id` = ".intval($category_id)." ORDER BY `id` ASC;");
        } else {
            $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 ORDER BY `id` ASC;");
        }
    }
    $stream_ids = Array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($full) {
                $return[] = $row;
            } else {
                $return[] = Array("id" => $row["id"]);
            }
            $stream_ids[] = $row["id"];
        }
    }
    $streams_sys = Array();
    $result = $db->query("SELECT * FROM `streams_sys` WHERE `stream_id` IN (".join(",", $stream_ids).");");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $streams_sys[intval($row["stream_id"])][intval($row["server_id"])] = $row;
        }
    }
    $activity = Array();
    $result = $db->query("SELECT `stream_id`, `server_id`, COUNT(`activity_id`) AS `active` FROM `user_activity_now` WHERE `stream_id` IN (".join(",", $stream_ids).") GROUP BY `stream_id`, `server_id`;");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $activity[intval($row["stream_id"])][intval($row["server_id"])] = $row["active"];
        }
    }
    if (count($return) > 0) {
        foreach (range(0, count($return)-1) as $i) {
            $return[$i]["servers"] = Array();
            foreach($streams_sys[intval($return[$i]["id"])] as $rServerID => $rStreamSys) {
                $rServerArray = Array("server_id" => $rServerID);
                if (isset($activity[intval($return[$i]["id"])][$rServerID])) {
                    $rServerArray["active_count"] = $activity[intval($return[$i]["id"])][$rServerID];
                } else {
                    $rServerArray["active_count"] = 0;
                }
                $rServerArray["uptime"] = 0;
                if (intval($return[$i]["direct_source"]) == 1) {
                    // Direct
                    $rServerArray["actual_status"] = 5;
                } else if ($rStreamSys["monitor_pid"]) {
                    // Started
                    if (($rStreamSys["pid"]) && ($rStreamSys["pid"] > 0)) {
                        // Running
                        $rServerArray["actual_status"] = 1;
                        $rServerArray["uptime"] = time() - intval($rStreamSys["stream_started"]);
                    } else {
                        if (intval($rStreamSys["stream_status"]) == 0) {
                            // Starting
                            $rServerArray["actual_status"] = 2;
                        } else {
                            // Stalled
                            $rServerArray["actual_status"] = 3;
                        }
                    }
                } else if (intval($rStreamSys["on_demand"]) == 1) {
                    // On Demand
                    $rServerArray["actual_status"] = 4;
                } else {
                    // Stopped
                    $rServerArray["actual_status"] = 0;
                }
                $rServerArray["current_source"] = $rStreamSys["current_source"];
                $rServerArray["uptime_text"] = sprintf('%02dh %02dm %02ds', ($rServerArray["uptime"]/3600),($rServerArray["uptime"]/60%60), ($rServerArray["uptime"]%60));
                $rServerArray["on_demand"] = $rStreamSys["on_demand"];
                $rStreamInfo = json_decode($rStreamSys["stream_info"], True);
                $rServerArray["stream_text"] = "<div style='font-size: 12px; text-align: center;'>Not Available</div>";
                if ($rServerArray["actual_status"] == 1) {
                    if (!isset($rStreamInfo["codecs"]["video"])) {
                        $rStreamInfo["codecs"]["video"] = "N/A";
                    }
                    if (!isset($rStreamInfo["codecs"]["audio"])) {
                        $rStreamInfo["codecs"]["audio"] = "N/A";
                    }
                    if ($rStreamSys['bitrate'] == 0) { 
                        $rStreamSys['bitrate'] = "?";
                    }
                    $rServerArray["stream_text"] = "<div style='font-size: 12px; text-align: center;'>
                        <div class='row'>
                            <div class='col'>".$rStreamSys['bitrate']." Kbps</div>
                            <div class='col' style='color: #20a009;'><i class='mdi mdi-video' data-name='mdi-video'></i></div>
                            <div class='col' style='color: #20a009;'><i class='mdi mdi-volume-high' data-name='mdi-volume-high'></i></div>
                        </div>
                        <div class='row'>
                            <div class='col'>".$rStreamInfo["codecs"]["video"]["width"]." x ".$rStreamInfo["codecs"]["video"]["height"]."</div>
                            <div class='col'>".$rStreamInfo["codecs"]["video"]["codec_name"]."</div>
                            <div class='col'>".$rStreamInfo["codecs"]["audio"]["codec_name"]."</div>
                        </div>
                    </div>";
                }
                $return[$i]["servers"][] = $rServerArray;
            }
        }
    }
    return $return;
}

function getConnections($rServerID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `server_id` = '".$db->real_escape_string($rServerID)."';");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getUserConnections($rUserID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `user_id` = '".$db->real_escape_string($rUserID)."';");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getEPGSources() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `epg`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}

function findEPG($rEPGName) {
    global $db;
    $result = $db->query("SELECT `id`, `data` FROM `epg`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            foreach (json_decode($row["data"], True) as $rChannelID => $rChannelData) {
                if ($rChannelID == $rEPGName) {
                    if (count($rChannelData["langs"]) > 0) {
                        $rEPGLang = $rChannelData["langs"][0];
                    } else {
                        $rEPGLang = "";
                    }
                    return Array("channel_id" => $rChannelID, "epg_lang" => $rEPGLang, "epg_id" => intval($row["id"]));
                }
            }
        }
    }
    return null;
}

function getStreamArguments() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_arguments` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["argument_key"]] = $row;
        }
    }
    return $return;
}

function getTranscodeProfiles() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `transcoding_profiles` ORDER BY `profile_id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getWatchFolders($rType=null) {
    global $db;
    $return = Array();
    if ($rType) {
        $result = $db->query("SELECT * FROM `watch_folders` WHERE `type` = '".$db->real_escape_string($rType)."' ORDER BY `id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `watch_folders` ORDER BY `id` ASC;");
    }
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getWatchCategories($rType=null) {
    global $db;
    $return = Array();
    if ($rType) {
        $result = $db->query("SELECT * FROM `watch_categories` WHERE `type` = ".intval($rType)." ORDER BY `genre_id` ASC;");
    } else {
        $result = $db->query("SELECT * FROM `watch_categories` ORDER BY `genre_id` ASC;");
    }
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["genre_id"]] = $row;
        }
    }
    return $return;
}

function getWatchFolder($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `watch_folders` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getSeriesByTMDB($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `series` WHERE `tmdb_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getSeries() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `series` ORDER BY `title` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getSerie($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `series` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getSeriesTrailer($rTMDBID) {
    // Not implemented in TMDB PHP API...
    global $rSettings, $rAdminSettings;
    if (strlen($rAdminSettings["tmdb_language"]) > 0) {
        $rURL = "https://api.themoviedb.org/3/tv/".$rTMDBID."/videos?api_key=".$rSettings["tmdb_api_key"]."&language=".$rAdminSettings["tmdb_language"];
    } else {
        $rURL = "https://api.themoviedb.org/3/tv/".$rTMDBID."/videos?api_key=".$rSettings["tmdb_api_key"];
    }
    $rJSON = json_decode(file_get_contents($rURL), True);
    foreach ($rJSON["results"] as $rVideo) {
        if ((strtolower($rVideo["type"]) == "trailer") && (strtolower($rVideo["site"]) == "youtube")) {
            return $rVideo["key"];
        }
    }
    return "";
}

function getStills($rTMDBID, $rSeason, $rEpisode) {
    // Not implemented in TMDB PHP API...
    global $rSettings, $rAdminSettings;
    if (strlen($rAdminSettings["tmdb_language"]) > 0) {
        $rURL = "https://api.themoviedb.org/3/tv/".$rTMDBID."/season/".$rSeason."/episode/".$rEpisode."/images?api_key=".$rSettings["tmdb_api_key"]."&language=".$rAdminSettings["tmdb_language"];
    } else {
        $rURL = "https://api.themoviedb.org/3/tv/".$rTMDBID."/season/".$rSeason."/episode/".$rEpisode."/images?api_key=".$rSettings["tmdb_api_key"];
    }
    return json_decode(file_get_contents($rURL), True);
}

function getUserAgents() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `blocked_user_agents` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getBlockedIPs() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `blocked_ips` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getRTMPIPs() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `rtmp_ips` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getStream($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `streams` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getUser($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `users` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getRegisteredUser($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `reg_users` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getRegisteredUserHash($rHash) {
    global $db;
    $result = $db->query("SELECT * FROM `reg_users` WHERE MD5(`username`) = '".$db->real_escape_string($rHash)."' LIMIT 1;");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getEPG($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `epg` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getStreamOptions($rID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_options` WHERE `stream_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["argument_id"])] = $row;
        }
    }
    return $return;
}

function getStreamSys($rID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_sys` WHERE `stream_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["server_id"])] = $row;
        }
    }
    return $return;
}

function getRegisteredUsers($rOwner=null, $rIncludeSelf=true) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `reg_users` ORDER BY `username` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ((!$rOwner) OR ($row["owner_id"] == $rOwner) OR (($row["id"] == $rOwner) && ($rIncludeSelf))) {
                $return[intval($row["id"])] = $row;
            }
        }
    }
    if (count($return) == 0) { $return[-1] = Array(); }
    return $return;
}

function hasPermissions($rType, $rID) {
    global $rUserInfo, $db, $rPermissions;
    if ($rType == "user") {
        if (in_array(intval(getUser($rID)["member_id"]), array_keys(getRegisteredUsers($rUserInfo["id"])))) {
            return true;
        }
    } else if ($rType == "pid") {
        $result = $db->query("SELECT `user_id` FROM `user_activity_now` WHERE `pid` = ".intval($rID).";");
        if (($result) && ($result->num_rows > 0)) {
            if (in_array(intval(getUser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getRegisteredUsers($rUserInfo["id"])))) {
                return true;
            }
        }
    } else if ($rType == "reg_user") {
        if ((in_array(intval($rID), array_keys(getRegisteredUsers($rUserInfo["id"])))) && (intval($rID) <> intval($rUserInfo["id"]))) {
            return true;
        }
    } else if ($rType == "ticket") {
        if (in_array(intval(getTicket($rID)["member_id"]), array_keys(getRegisteredUsers($rUserInfo["id"])))) {
            return true;
        }
    } else if ($rType == "mag") {
        $result = $db->query("SELECT `user_id` FROM `mag_devices` WHERE `mag_id` = ".intval($rID).";");
        if (($result) && ($result->num_rows > 0)) {
            if (in_array(intval(getUser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getRegisteredUsers($rUserInfo["id"])))) {
                return true;
            }
        }
    } else if ($rType == "e2") {
        $result = $db->query("SELECT `user_id` FROM `enigma2_devices` WHERE `device_id` = ".intval($rID).";");
        if (($result) && ($result->num_rows > 0)) {
            if (in_array(intval(getUser($result->fetch_assoc()["user_id"])["member_id"]), array_keys(getRegisteredUsers($rUserInfo["id"])))) {
                return true;
            }
        }
    } else if (($rType == "adv") && ($rPermissions["is_admin"])) {
		if ((count($rPermissions["advanced"]) > 0) && ($rUserInfo["member_group_id"] <> 1)) {
			return in_array($rID, $rPermissions["advanced"]);
		} else {
			return true;
		}
	}
    return false;
}

function getMemberGroups() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `member_groups` ORDER BY `group_id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["group_id"])] = $row;
        }
    }
    return $return;
}

function getMemberGroup($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `member_groups` WHERE `group_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getRegisteredUsernames() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `id`, `username` FROM `reg_users` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row["username"];
        }
    }
    return $return;
}

function getOutputs($rUser=null) {
    global $db;
    $return = Array();
    if ($rUser) {
        $result = $db->query("SELECT `access_output_id` FROM `user_output` WHERE `user_id` = ".intval($rUser).";");
    } else {
        $result = $db->query("SELECT * FROM `access_output` ORDER BY `access_output_id` ASC;");
    }
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ($rUser) {
                $return[] = $row["access_output_id"];
            } else {
                $return[] = $row;
            }
        }
    }
    return $return;
}

function getBouquets() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `bouquets` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getBouquet($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `bouquets` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function addToBouquet($rType, $rBouquetID, $rID) {
    global $db;
    $rBouquet = getBouquet($rBouquetID);
    if ($rBouquet) {
        if ($rType == "stream") {
            $rColumn = "bouquet_channels";
        } else {
            $rColumn = "bouquet_series";
        }
        $rChannels = json_decode($rBouquet[$rColumn], True);
        if (!in_array($rID, $rChannels)) {
            $rChannels[] = $rID;
            if (count($rChannels) > 0) {
                $db->query("UPDATE `bouquets` SET `".$rColumn."` = '".$db->real_escape_string(json_encode(array_values($rChannels)))."' WHERE `id` = ".intval($rBouquetID).";");
            }
        }
    }
}

function removeFromBouquet($rType, $rBouquetID, $rID) {
    global $db;
    $rBouquet = getBouquet($rBouquetID);
    if ($rBouquet) {
        if ($rType == "stream") {
            $rColumn = "bouquet_channels";
        } else {
            $rColumn = "bouquet_series";
        }
        $rChannels = json_decode($rBouquet[$rColumn], True);
        if (($rKey = array_search($rID, $rChannels)) !== false) {
            unset($rChannels[$rKey]);
            $db->query("UPDATE `bouquets` SET `".$rColumn."` = '".$db->real_escape_string(json_encode(array_values($rChannels)))."' WHERE `id` = ".intval($rBouquetID).";");
        }
    }
}

function getPackages($rGroup=null) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `packages` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ((!isset($rGroup)) OR (in_array(intval($rGroup), json_decode($row["groups"], True)))) {
                $return[intval($row["id"])] = $row;
            }
        }
    }
    return $return;
}

function getPackage($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `packages` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getTranscodeProfile($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `transcoding_profiles` WHERE `profile_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getUserAgent($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `blocked_user_agents` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getBlockedIP($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `blocked_ips` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getRTMPIP($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `rtmp_ips` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getEPGs() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `epg` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getCategories($rType="live") {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '".$db->real_escape_string($rType)."' ORDER BY `cat_order` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getChannels($rType="live") {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '".$db->real_escape_string($rType)."' ORDER BY `cat_order` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getChannelsByID($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `streams` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getCategory($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getMag($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `mag_devices` WHERE `mag_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = ".intval($row["user_id"]).";");
        if (($result) && ($result->num_rows == 1)) {
            $magrow = $result->fetch_assoc();
            $row["paired_user"] = $magrow["pair_id"];
            $row["username"] = getUser($row["paired_user"])["username"];
        }
        return $row;
    }
    return Array();
}

function getEnigma($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `enigma2_devices` WHERE `device_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = ".intval($row["user_id"]).";");
        if (($result) && ($result->num_rows == 1)) {
            $e2row = $result->fetch_assoc();
            $row["paired_user"] = $e2row["pair_id"];
            $row["username"] = getUser($row["paired_user"])["username"];
        }
        return $row;
    }
    return Array();
}

function getMAGUser($rID) {
    global $db;
    $result = $db->query("SELECT `mac` FROM `mag_devices` WHERE `user_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return base64_decode($result->fetch_assoc()["mac"]);
    }
    return "";
}

function getE2User($rID) {
    global $db;
    $result = $db->query("SELECT `mac` FROM `enigma2_devices` WHERE `user_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc()["mac"];
    }
    return "";
}

function getTicket($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `tickets` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows > 0)) {
        $row = $result->fetch_assoc();
        $row["replies"] = Array();
        $row["title"] = htmlspecialchars($row["title"]);
        $result = $db->query("SELECT * FROM `tickets_replies` WHERE `ticket_id` = ".intval($rID)." ORDER BY `date` ASC;");
        while ($reply = $result->fetch_assoc()) {
            // Hack to fix display issues on short text.
            $reply["message"] = htmlspecialchars($reply["message"]);
            if (strlen($reply["message"]) < 80) {
                $reply["message"] .= str_repeat("&nbsp; ", 80-strlen($reply["message"]));
            }
            $row["replies"][] = $reply;
        }
        $row["user"] = getRegisteredUser($row["member_id"]);
        return $row;
    }
    return null;
}

function getExpiring($rID) {
	global $db;
	$rAvailableMembers = array_keys(getRegisteredUsers($rID));
	$return = Array();
	$result = $db->query("SELECT `id`, `member_id`, `username`, `password`, `exp_date` FROM `users` WHERE `member_id` IN (".join(",", $rAvailableMembers).") AND `exp_date` >= UNIX_TIMESTAMP() ORDER BY `exp_date` ASC LIMIT 100;");
	if (($result) && ($result->num_rows > 0)) {
		while ($row = $result->fetch_assoc()) {
			$return[] = $row;
		}
	}
	return $return;
}

function getTickets($rID=null) {
    global $db;
    $return = Array();
    if ($rID) {
        $result = $db->query("SELECT `tickets`.`id`, `tickets`.`member_id`, `tickets`.`title`, `tickets`.`status`, `tickets`.`admin_read`, `tickets`.`user_read`, `reg_users`.`username` FROM `tickets`, `reg_users` WHERE `member_id` = ".intval($rID)." AND `reg_users`.`id` = `tickets`.`member_id` ORDER BY `id` DESC;");
    } else {
        $result = $db->query("SELECT `tickets`.`id`, `tickets`.`member_id`, `tickets`.`title`, `tickets`.`status`, `tickets`.`admin_read`, `tickets`.`user_read`, `reg_users`.`username` FROM `tickets`, `reg_users` WHERE `reg_users`.`id` = `tickets`.`member_id` ORDER BY `id` DESC;");
    }
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $dateresult = $db->query("SELECT MIN(`date`) AS `date` FROM `tickets_replies` WHERE `ticket_id` = ".intval($row["id"])." AND `admin_reply` = 0;");
            if ($rDate = $dateresult->fetch_assoc()["date"]) {
                $row["created"] = date("Y-m-d H:i", $rDate);
            } else {
                $row["created"] = "";
            }
            $dateresult = $db->query("SELECT MAX(`date`) AS `date` FROM `tickets_replies` WHERE `ticket_id` = ".intval($row["id"])." AND `admin_reply` = 1;");
            if ($rDate = $dateresult->fetch_assoc()["date"]) {
                $row["last_reply"] = date("Y-m-d H:i", $rDate);
            } else {
                $row["last_reply"] = "";
            }
            if ($row["status"] <> 0) {
                if ($row["user_read"] == 0) {
                    $row["status"] = 2;
                }
                if ($row["admin_read"] == 1) {
                    $row["status"] = 3;
                }
            }
            $return[] = $row;
        }
    }
    return $return;
}

function checkTrials() {
    global $db, $rPermissions, $rUserInfo;
    $rTotal = $rPermissions["total_allowed_gen_trials"];
    if ($rTotal > 0) {
        $rTotalIn = $rPermissions["total_allowed_gen_in"];
        if ($rTotalIn == "hours") {
            $rTime = time() - (intval($rTotal) * 3600);
        } else {
            $rTime = time() - (intval($rTotal) * 3600 * 24);
        }
        $result = $db->query("SELECT COUNT(`id`) AS `count` FROM `users` WHERE `member_id` = ".intval($rUserInfo["id"])." AND `created_at` >= ".$rTime." AND `is_trial` = 1;");
        return $result->fetch_assoc()["count"] < $rTotal;
    }
    return false;
}

function cryptPassword($password, $salt="xtreamcodes", $rounds=20000) {
    if ($salt == "") {
        $salt = substr(bin2hex(openssl_random_pseudo_bytes(16)),0,16);
    }
    $hash = crypt($password, sprintf('$6$rounds=%d$%s$', $rounds, $salt));
    return $hash;
}

function getIP(){
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getID() {
	if (file_exists(MAIN_DIR."adtools/settings.json")) {
		return json_decode(file_get_contents(MAIN_DIR."adtools/settings.json"), True)["rid"];
	}
	return 0;
}

function getPermissions($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `member_groups` WHERE `group_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function doLogin($rUsername, $rPassword) {
    global $db;
    $result = $db->query("SELECT `id`, `username`, `password`, `member_group_id`, `google_2fa_sec`, `status` FROM `reg_users` WHERE `username` = '".$db->real_escape_string($rUsername)."' LIMIT 1;");
    if (($result) && ($result->num_rows == 1)) {
        $rRow = $result->fetch_assoc();
        if (cryptPassword($rPassword) == $rRow["password"]) {
            return $rRow;
        }
    }
    return null;
}

function getSubresellerSetups() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `subreseller_setup` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getSubresellerSetup($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `subreseller_setup` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return null;
}

function getEpisodeParents() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `series_episodes`.`stream_id`, `series`.`id`, `series`.`title` FROM `series_episodes` LEFT JOIN `series` ON `series`.`id` = `series_episodes`.`series_id`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["stream_id"])] = $row;
        }
    }
    return $return;
}

function getSeriesList() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `id`, `title` FROM `series` ORDER BY `title` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function checkTable($rTable) {
    global $db;
    $rTableQuery = Array(
        "subreseller_setup" => Array("CREATE TABLE `subreseller_setup` (`id` int(11) NOT NULL AUTO_INCREMENT, `reseller` int(8) NOT NULL DEFAULT '0', `subreseller` int(8) NOT NULL DEFAULT '0', `status` int(1) NOT NULL DEFAULT '1', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"),
        "admin_settings" => Array("CREATE TABLE `admin_settings` (`type` varchar(128) NOT NULL DEFAULT '', `value` varchar(4096) NOT NULL DEFAULT '', PRIMARY KEY (`type`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"),
        "watch_folders" => Array("CREATE TABLE `watch_folders` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` varchar(32) NOT NULL DEFAULT '', `directory` varchar(2048) NOT NULL DEFAULT '', `server_id` int(8) NOT NULL DEFAULT '0', `category_id` int(8) NOT NULL DEFAULT '0', `bouquets` varchar(4096) NOT NULL DEFAULT '[]', `last_run` int(32) NOT NULL DEFAULT '0', `active` int(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"),
        "tmdb_async" => Array("CREATE TABLE `tmdb_async` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `stream_id` int(16) NOT NULL DEFAULT '0', `status` int(8) NOT NULL DEFAULT '0', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"),
        "watch_settings" => Array("CREATE TABLE `watch_settings` (`read_native` int(1) NOT NULL DEFAULT '1', `movie_symlink` int(1) NOT NULL DEFAULT '1', `auto_encode` int(1) NOT NULL DEFAULT '0', `transcode_profile_id` int(8) NOT NULL DEFAULT '0', `scan_seconds` int(8) NOT NULL DEFAULT '3600') ENGINE=InnoDB DEFAULT CHARSET=latin1;", "INSERT INTO `watch_settings` (`read_native`, `movie_symlink`, `auto_encode`, `transcode_profile_id`, `scan_seconds`) VALUES(1, 1, 0, 0, 3600);"),
        "watch_categories" => Array("CREATE TABLE `watch_categories` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `genre_id` int(8) NOT NULL DEFAULT '0', `genre` varchar(64) NOT NULL DEFAULT '', `category_id` int(8) NOT NULL DEFAULT '0', `bouquets` varchar(4096) NOT NULL DEFAULT '[]', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1", "INSERT INTO `watch_categories` (`id`, `type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES (1, 1, 28, 'Action', 0, '[]'), (2, 1, 12, 'Adventure', 0, '[]'), (3, 1, 16, 'Animation', 0, '[]'), (4, 1, 35, 'Comedy', 0, '[]'), (5, 1, 80, 'Crime', 0, '[]'), (6, 1, 99, 'Documentary', 0, '[]'), (7, 1, 18, 'Drama', 0, '[]'), (8, 1, 10751, 'Family', 0, '[]'), (9, 1, 14, 'Fantasy', 0, '[]'), (10, 1, 36, 'History', 0, '[]'), (11, 1, 27, 'Horror', 0, '[]'), (12, 1, 10402, 'Music', 0, '[]'), (13, 1, 9648, 'Mystery', 0, '[]'), (14, 1, 10749, 'Romance', 0, '[]'), (15, 1, 878, 'Science Fiction', 0, '[]'), (16, 1, 10770, 'TV Movie', 0, '[]'), (17, 1, 53, 'Thriller', 0, '[]'), (18, 1, 10752, 'War', 0, '[]'), (19, 1, 37, 'Western', 0, '[]');"),
        "watch_output" => Array("CREATE TABLE `watch_output` (`id` int(11) NOT NULL AUTO_INCREMENT, `type` int(1) NOT NULL DEFAULT '0', `server_id` int(8) NOT NULL DEFAULT '0', `filename` varchar(4096) NOT NULL DEFAULT '', `status` int(1) NOT NULL DEFAULT '0', `stream_id` int(8) NOT NULL DEFAULT '0', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"),
		"login_flood" => Array("CREATE TABLE `login_flood` (`id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(128) NOT NULL DEFAULT '', `ip` varchar(64) NOT NULL DEFAULT '', `dateadded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;")
    );
    if ((!$db->query("DESCRIBE `".$rTable."`;")) && (isset($rTableQuery[$rTable]))) {
        // Doesn't exist! Create it.
        foreach ($rTableQuery[$rTable] as $rQuery) {
            $db->query($rQuery);
        }
    }
}

function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;
    $days = floor($inputSeconds / $secondsInADay);
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}

function getAdminSettings() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `type`, `value` FROM `admin_settings`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["type"]] = $row["value"];
        }
    }
    return $return;
}

function writeAdminSettings() {
    global $rAdminSettings, $db;
    foreach ($rAdminSettings as $rKey => $rValue) {
        if (strlen($rKey) > 0) {
            $db->query("REPLACE INTO `admin_settings`(`type`, `value`) VALUES('".$db->real_escape_string($rKey)."', '".$db->real_escape_string($rValue)."');");
        }
    }
}

function downloadImage($rImage) {
    if ((strlen($rImage) > 0) && (substr(strtolower($rImage), 0, 4) == "http")) {
        $rPathInfo = pathinfo($rImage);
        $rExt = $rPathInfo["extension"];
        if (in_array(strtolower($rExt), Array("jpg", "jpeg", "png"))) {
            $rPrevPath = MAIN_DIR . "wwwdir/images/".$rPathInfo["filename"].".".$rExt;
			if (file_exists($rPrevPath)) { 
				return getURL()."/images/".$rPathInfo["filename"].".".$rExt;
			} else {
				$rData = file_get_contents($rImage);
				if (strlen($rData) > 0) {
                    $rFilename = md5($rPathInfo["filename"]);
                    $rPath = MAIN_DIR . "wwwdir/images/".$rFilename.".".$rExt;
					file_put_contents($rPath, $rData);
					if (strlen(file_get_contents($rPath)) == strlen($rData)) {
						return getURL()."/images/".$rFilename.".".$rExt;
					}
				}
			}
        }
    }
    return $rImage;
}

function updateSeries($rID) {
    global $db, $rSettings, $rAdminSettings;
    require_once("tmdb.php");
    $result = $db->query("SELECT `tmdb_id` FROM `series` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        $rTMDBID = $result->fetch_assoc()["tmdb_id"];
        if (strlen($rTMDBID) > 0) {
            if (strlen($rAdminSettings["tmdb_language"]) > 0) {
                $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
            } else {
                $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
            }
            $rReturn = Array();
            $rSeasons = json_decode($rTMDB->getTVShow($rTMDBID)->getJSON(), True)["seasons"];
            foreach ($rSeasons as $rSeason) {
                if ($rAdminSettings["download_images"]) {
                    $rSeason["cover"] = downloadImage("https://image.tmdb.org/t/p/w600_and_h900_bestv2".$rSeason["poster_path"]);
                } else {
                    $rSeason["cover"] = "https://image.tmdb.org/t/p/w600_and_h900_bestv2".$rSeason["poster_path"];
                }
                $rSeason["cover_big"] = $rSeason["cover"];
                unset($rSeason["poster_path"]);
                $rReturn[] = $rSeason;
            }
            $db->query("UPDATE `series` SET `seasons` = '".$db->real_escape_string(json_encode($rReturn))."' WHERE `id` = ".intval($rID).";");
        }
    }
}

function getFooter() {
    // Don't be a dick. Leave it.
    global $rAdminSettings, $rPermissions, $rSettings, $rRelease, $rEarlyAccess;
    if ($rPermissions["is_admin"]) {
		if ($rEarlyAccess) {
			return "Copyright &copy; ".date("Y")." - <a href=\"https://xtream-ui.com\">Xtream UI</a> R".$rRelease.$rEarlyAccess." - Early Access";
		} else {
			return "Copyright &copy; ".date("Y")." - <a href=\"https://xtream-ui.com\">Xtream UI</a> R".$rRelease." - Free & Open Source Forever";
		}
    } else {
        return $rSettings["copyrights_text"];
    }
}

function getURL() {
    global $rServers, $_INFO;
    if (strlen($rServers[$_INFO["server_id"]]["domain_name"]) > 0) {
        return "http://".$rServers[$_INFO["server_id"]]["domain_name"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"];
    } else if (strlen($rServers[$_INFO["server_id"]]["vpn_ip"]) > 0) {
        return "http://".$rServers[$_INFO["server_id"]]["vpn_ip"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"];
    } else {
        return "http://".$rServers[$_INFO["server_id"]]["server_ip"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"];
    }
}

function resetSettings() {
    global $db, $rResetSettings;
    foreach ($rResetSettings as $rKey => $rValue) {
        $db->query("UPDATE `settings` SET `".$db->real_escape_string($rKey)."` = '".$db->real_escape_string($rValue)."';");
    }
}

function scanBouquets() {
    global $db;
    $rStreamIDs = Array(0 => Array(), 1 => Array());
    $result = $db->query("SELECT `id` FROM `streams`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $rStreamIDs[0][] = intval($row["id"]);
        }
    }
    $result = $db->query("SELECT `id` FROM `series`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $rStreamIDs[1][] = intval($row["id"]);
        }
    }
    foreach (getBouquets() as $rID => $rBouquet) {
        $rUpdate = Array(0 => Array(), 1 => Array());
        foreach (json_decode($rBouquet["bouquet_channels"], True) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[0])) {
                $rUpdate[0][] = intval($rID);
            }
        }
        foreach (json_decode($rBouquet["bouquet_series"], True) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[1])) {
                $rUpdate[1][] = intval($rID);
            }
        }
        $db->query("UPDATE `bouquets` SET `bouquet_channels` = '".$db->real_escape_string(json_encode($rUpdate[0]))."', `bouquet_series` = '".$db->real_escape_string(json_encode($rUpdate[1]))."' WHERE `id` = ".intval($rBouquet["id"]).";");
    }
}

function scanBouquet($rID) {
    global $db;
    $rBouquet = getBouquet($rID);
    if ($rBouquet) {
        $rStreamIDs = Array();
        $result = $db->query("SELECT `id` FROM `streams`;");
        if (($result) && ($result->num_rows > 0)) {
            while ($row = $result->fetch_assoc()) {
                $rStreamIDs[0][] = intval($row["id"]);
            }
        }
        $result = $db->query("SELECT `id` FROM `series`;");
        if (($result) && ($result->num_rows > 0)) {
            while ($row = $result->fetch_assoc()) {
                $rStreamIDs[1][] = intval($row["id"]);
            }
        }
        $rUpdate = Array(0 => Array(), 1 => Array());
        foreach (json_decode($rBouquet["bouquet_channels"], True) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[0])) {
                $rUpdate[0][] = intval($rID);
            }
        }
        foreach (json_decode($rBouquet["bouquet_series"], True) as $rID) {
            if (in_array(intval($rID), $rStreamIDs[1])) {
                $rUpdate[1][] = intval($rID);
            }
        }
        $db->query("UPDATE `bouquets` SET `bouquet_channels` = '".$db->real_escape_string(json_encode($rUpdate[0]))."', `bouquet_series` = '".$db->real_escape_string(json_encode($rUpdate[1]))."' WHERE `id` = ".intval($rBouquet["id"]).";");
    }
}

function getNextOrder() {
    global $db;
    $result = $db->query("SELECT MAX(`order`) AS `order` FROM `streams`;");
    if (($result) && ($result->num_rows == 1)) {
        return intval($result->fetch_assoc()["order"]) + 1;
    }
    return 0;
}

function generateSeriesPlaylist($rSeriesNo) {
    global $db, $rServers, $rSettings;
    $rReturn = Array("success" => false, "sources" => Array(), "server_id" => 0);
    $result = $db->query("SELECT `stream_id` FROM `series_episodes` WHERE `series_id` = ".intval($rSeriesNo)." ORDER BY `season_num` ASC, `sort` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $resultB = $db->query("SELECT `stream_source` FROM `streams` WHERE `id` = ".intval($row["stream_id"]).";");
            if (($resultB) && ($resultB->num_rows > 0)) {
                $rSource = json_decode($resultB->fetch_assoc()["stream_source"], True)[0];
                $rSplit = explode(":", $rSource);
                $rFilename = join(":", array_slice($rSplit, 2, count($rSplit)-2));
                $rServerID = intval($rSplit[1]);
                if ($rReturn["server_id"] == 0) {
                    $rReturn["server_id"] = $rServerID;
                    $rReturn["success"] = true;
                }
                if ($rReturn["server_id"] <> $rServerID) {
                    $rReturn["success"] = false;
                    break;
                }
                $rReturn["sources"][] = $rFilename;
            }
        }
    }
    return $rReturn;
}

function flushIPs() {
    global $db, $rServers;
    $rCommand = "sudo /sbin/iptables -P INPUT ACCEPT && sudo /sbin/iptables -P OUTPUT ACCEPT && sudo /sbin/iptables -P FORWARD ACCEPT && sudo /sbin/iptables -F";
    foreach ($rServers as $rServer) {
        sexec($rServer["id"], $rCommand);
    }
    $db->query("DELETE FROM `blocked_ips`;");
}

function updateTables() {
    global $db;
    if (file_exists("./.update")) {
        unlink("./.update");
    }
    // Update table settings etc.
    checkTable("tmdb_async");
    checkTable("subreseller_setup");
    checkTable("admin_settings");
    checkTable("watch_folders");
    checkTable("watch_settings");
    checkTable("watch_categories");
    checkTable("watch_output");
	checkTable("login_flood");
    // R19 Early Access
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'bouquets';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `category_id` int(8) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `bouquets` varchar(4096) NOT NULL DEFAULT '[]';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_settings` LIKE 'percentage_match';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `watch_settings` ADD COLUMN `percentage_match` int(3) NOT NULL DEFAULT '80';");
        $db->query("ALTER TABLE `watch_settings` ADD COLUMN `ffprobe_input` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'disable_tmdb';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `disable_tmdb` int(1) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `ignore_no_match` int(1) NOT NULL DEFAULT '0';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `auto_subtitles` int(1) NOT NULL DEFAULT '0';");
    }
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'fb_bouquets';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `fb_bouquets` VARCHAR(4096) NOT NULL DEFAULT '[]';");
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `fb_category_id` int(8) NOT NULL DEFAULT '0';");
    }
    // R19 Official
    $rResult = $db->query("SHOW COLUMNS FROM `watch_folders` LIKE 'allowed_extensions';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `watch_folders` ADD COLUMN `allowed_extensions` VARCHAR(4096) NOT NULL DEFAULT '[]';");
    }
	// R20 Official
	$db->query("UPDATE `streams_arguments` SET `argument_cmd` = '-cookies \'%s\'' WHERE `id` = 17;");
	// R21 Early Access
	$db->query("INSERT IGNORE INTO `streams_arguments` VALUES (19, 'fetch', 'Headers', 'Set Custom Headers', 'http', 'headers', '-headers \"%s\"', 'text', NULL);");
	$rResult = $db->query("SHOW COLUMNS FROM `reg_users` LIKE 'dark_mode';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `reg_users` ADD COLUMN `dark_mode` int(1) NOT NULL DEFAULT '0';");
		$db->query("ALTER TABLE `reg_users` ADD COLUMN `sidebar` int(1) NOT NULL DEFAULT '0';");
    }
	$rResult = $db->query("SHOW COLUMNS FROM `member_groups` LIKE 'minimum_trial_credits';");
    if (($rResult) && ($rResult->num_rows == 0)) {
        $db->query("ALTER TABLE `member_groups` ADD COLUMN `minimum_trial_credits` int(16) NOT NULL DEFAULT '0';");
    }
	// Update Categories
	updateTMDbCategories();
}

function updateTMDbCategories() {
    global $db, $rAdminSettings, $rSettings;
    include "tmdb.php";
    if (strlen($rAdminSettings["tmdb_language"]) > 0) {
        $rTMDB = new TMDB($rSettings["tmdb_api_key"], $rAdminSettings["tmdb_language"]);
    } else {
        $rTMDB = new TMDB($rSettings["tmdb_api_key"]);
    }
    $rCurrentCats = Array(1 => Array(), 2 => Array());
    $rResult = $db->query("SELECT `type`, `genre_id` FROM `watch_categories`;");
    if (($rResult) && ($rResult->num_rows > 0)) {
        while ($rRow = $rResult->fetch_assoc()) {
            $rCurrentCats[$rRow["type"]][] = $rRow["genre_id"];
        }
    }
    $rMovieGenres = $rTMDB->getMovieGenres();
    foreach ($rMovieGenres as $rMovieGenre) {
        if (!in_array($rMovieGenre->getID(), $rCurrentCats[1])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(1, ".intval($rMovieGenre->getID()).", '".$db->real_escape_string($rMovieGenre->getName())."', 0, '[]');");
        }
        if (!in_array($rMovieGenre->getID(), $rCurrentCats[2])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(2, ".intval($rMovieGenre->getID()).", '".$db->real_escape_string($rMovieGenre->getName())."', 0, '[]');");
        }
    }
    $rTVGenres = $rTMDB->getTVGenres();
    foreach ($rTVGenres as $rTVGenre) {
        if (!in_array($rTVGenre->getID(), $rCurrentCats[1])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(1, ".intval($rTVGenre->getID()).", '".$db->real_escape_string($rTVGenre->getName())."', 0, '[]');");
        }
        if (!in_array($rTVGenre->getID(), $rCurrentCats[2])) {
            $db->query("INSERT INTO `watch_categories`(`type`, `genre_id`, `genre`, `category_id`, `bouquets`) VALUES(2, ".intval($rTVGenre->getID()).", '".$db->real_escape_string($rTVGenre->getName())."', 0, '[]');");
        }
    }
}

if (isset($_SESSION['hash'])) {
    $rUserInfo = getRegisteredUserHash($_SESSION['hash']);
    $rPermissions = getPermissions($rUserInfo['member_group_id']);
    if ($rPermissions["is_admin"]) {
        $rPermissions["is_reseller"] = 0; // Don't allow Admin & Reseller!
    }
	$rPermissions["advanced"] = json_decode($rPermissions["allowed_pages"], True);
    if ((!$rUserInfo) or (!$rPermissions) or ((!$rPermissions["is_admin"]) && (!$rPermissions["is_reseller"])) or ($_SESSION['ip'] <> getIP())) {
        unset($rUserInfo);
        unset($rPermissions);
        session_unset();
        session_destroy();
        header("Location: ./index.php");
    }
    $rAdminSettings = getAdminSettings();
	$rSettings = getSettings();
	$rAdminSettings["dark_mode"] = $rUserInfo["dark_mode"];
	$rSettings["sidebar"] = $rUserInfo["sidebar"];
    $rCategories = getCategories();
    $rServers = getStreamingServers();
    $rServerError = False;
    foreach ($rServers as $rServer) {
        if (((((time() - $rServer["last_check_ago"]) > 360)) OR ($rServer["status"] == 2)) AND ($rServer["can_delete"] == 1) AND ($rServer["status"] <> 3)) { $rServerError = True; }
        if (($rServer["status"] == 3) && ($rServer["last_check_ago"] > 0)) {
            $db->query("UPDATE `streaming_servers` SET `status` = 1 WHERE `id` = ".intval($rServer["id"]).";");
            $rServers[intval($rServer["id"])]["status"] = 1;
        }
    }
}
?>