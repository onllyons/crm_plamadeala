<?php

require_once __DIR__ . "/../vendors/MobileDetect/MobileDetect.php";

$recoveredPage = mysqli_real_escape_string($conMain, $_SERVER['REQUEST_URI']);
$cookie = json_decode($_COOKIE["analytics"] ?? "[]", true);
$plusTime = (bool)($_POST["plusTime"] ?? false);
$ipAddress = getIp();

// && !preg_match("/^\/adm\/pages/", $recoveredPage)
if ($recoveredPage) {
    // Check the time counter, if it is more than 30, then allow to update the table
    if ($plusTime) {
        // [counter, lastTimeUpdate]
        $currPageTimeCounter = $_SESSION["lengthStayOnPage_{$recoveredPage}"] ?? null;
        $needUpdateTime = false;

        if ($currPageTimeCounter) {
            if (time() - $currPageTimeCounter[1] >= 10) $currPageTimeCounter[0]++;
        } else {
            $currPageTimeCounter = [1, time()];
        }

        // 30 seconds
        if ($currPageTimeCounter[0] > 3) {
            $needUpdateTime = true;
            $currPageTimeCounter = [1, time()];
        }

        $_SESSION["lengthStayOnPage_{$recoveredPage}"] = $currPageTimeCounter;

        if (!$needUpdateTime) exit();
    }

    // Get userKey
    $userIdKey = $_SESSION["user"]["id"] ?? null;

    if (!$userIdKey) {
        $userIdKey = $_COOKIE["ID_KEY"] ?? $_SESSION["ID_KEY"] ?? null;

        if (!$userIdKey) {
            $userIdKey = randomHash();
            $_SESSION["ID_KEY"] = $userIdKey;
            setcookie("ID_KEY", $userIdKey, time() + (86400 * 30), "/");
        }
    }

    // Get current analytic key
    $secretKey = "f7c9a8d6e3b4c5f2a1d0e8f7b6c3a9d2e5f1c4b7a0d3e9f6c2b5a8d1e4f0b7c9";
    $analyticKey = hash_hmac("sha512", ($userIdKey . $recoveredPage . date("Y-m-d")), $secretKey);

    $userId = isAuth() ? $_SESSION["user"]["id"] : -1;

    if ($plusTime) {
        $stmt = mysqli_prepare($conMain, "UPDATE `visitorBehaviorAnalytics` SET `browserWindowWidth` = ?, `lengthStayOnPage` = lengthStayOnPage + 30 WHERE `hash` = ?");
        mysqli_stmt_bind_param($stmt, "ss", $width, $analyticKey);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        // Verificați dacă datele pentru această accesare a paginii au fost deja inserate în baza de date
        $stmt = mysqli_prepare($conMain, "SELECT `id` FROM `visitorBehaviorAnalytics` WHERE `hash` = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $analyticKey);
        mysqli_stmt_execute($stmt);
        $visitorAnalytics = mysqli_fetch_array(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        if (!$visitorAnalytics) {
            // Get geolocation data using ipinfo.io API
            $date = date("Y-m-d");
            $country = "";
            $region = "";
            $city = "";
            $timezone = "";

            $geoIpChecked = $_COOKIE["geoIpChecked"] ?? $_SESSION["geoIpChecked"] ?? null;

            if (!$geoIpChecked) {
                try {
                    $geoplugin = json_decode(@file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ipAddress));

                    if ($geoplugin) {
                        $country = $geoplugin->geoplugin_countryName ?? "";
                        $region = $geoplugin->geoplugin_region ?? "";
                        $city = $geoplugin->geoplugin_city ?? "";
                        $timezone = $geoplugin->geoplugin_timezone ?? "";

                        $_SESSION["geoIpChecked"] = $userIdKey;
                        setcookie("geoIpChecked", $userIdKey, time() + (86400 * 7), "/");
                    }
                } catch (Exception $e) {
                }
            }

            $browser = getBrowser();

            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $historyToPage = $_SERVER['HTTP_REFERER'] ?? "";

            $detect = new \Detection\MobileDetect;
            $deviceName = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Phone') : 'Computer');
            $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? "", 0, 2); // extrage primul cod de limbă

            if ($historyToPage === $_SERVER["SCRIPT_URI"]) $historyToPage = "";

            // Inserați datele în baza de date
            $stmt = mysqli_prepare($conMain, "INSERT INTO `visitorBehaviorAnalytics` (
                 `id`,
                 `hash`,
                 `user_id`,
                 `ipAddress`,
                 `recoveredPage`,
                 `country`,
                 `region`,
                 `city`,
                 `timezone`,
                 `browserVersion`,
                 `deviceName`,
                 `operatingSystem`,
                 `browserLanguage`,
                 `lengthStayOnPage`,
                 `historyToPage`,
                 `date`,
                 `time`
            ) VALUES (
                NULL,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                0,
                ?,
                ?,
                UNIX_TIMESTAMP()
            )
            ");
            mysqli_stmt_bind_param($stmt, "sissssssssssss", $analyticKey, $userId, $ipAddress, $recoveredPage, $country, $region, $city, $timezone, $browser["name"], $deviceName, $browser["operatingSystem"], $language, $historyToPage, $date);
            mysqli_stmt_execute($stmt);
            $id = mysqli_insert_id($conMain);
            mysqli_stmt_close($stmt);
        } else {
            $id = $visitorAnalytics["id"];
        }
    }
}

if (isset($_POST["plusTime"])) exit();

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $bname = 'Unknown';
    $operatingSystem = 'Unknown';
    $version = "version unknown";

    if ($u_agent) {
        $osList = array(
            /* -- WINDOWS -- */
            'Windows 10' => 'windows nt 10.0',
            'Windows 8.1' => 'windows nt 6.3',
            'Windows 8' => 'windows nt 6.2',
            'Windows 7' => 'windows nt 6.1',
            'Windows Vista' => 'windows nt 6.0',
            'Windows Server 2003' => 'windows nt 5.2',
            'Windows XP' => 'windows nt 5.1',
            'Windows 2000 sp1' => 'windows nt 5.01',
            'Windows 2000' => 'windows nt 5.0',
            'Windows NT 4.0' => 'windows nt 4.0',
            'Windows Me' => 'win 9x 4.9',
            'Windows 98' => 'windows 98',
            'Windows 95' => 'windows 95',
            'Windows CE' => 'windows ce',
            'Windows (version unknown)' => 'windows',
            /* -- MAC OS X -- */
            'Mac OS X Beta (Kodiak)' => 'Mac OS X beta',
            'Mac OS X Cheetah' => 'Mac OS X 10.0',
            'Mac OS X Puma' => 'Mac OS X 10.1',
            'Mac OS X Jaguar' => 'Mac OS X 10.2',
            'Mac OS X Panther' => 'Mac OS X 10.3',
            'Mac OS X Tiger' => 'Mac OS X 10.4',
            'Mac OS X Leopard' => 'Mac OS X 10.5',
            'Mac OS X Snow Leopard' => 'Mac OS X 10.6',
            'Mac OS X Lion' => 'Mac OS X 10.7',
            'Mac OS X Mountain Lion' => 'Mac OS X 10.8',
            'Mac OS X Mavericks' => 'Mac OS X 10.9',
            'Mac OS X Yosemite' => 'Mac OS X 10.10',
            'Mac OS X El Capitan' => 'Mac OS X 10.11',
            'macOS Sierra' => 'Mac OS X 10.12',
            'Mac OS X (version unknown)' => 'Mac OS X',
            'Mac OS (classic)' => '(mac_powerpc)|(macintosh)',
            /* -- OTHERS -- */
            'OpenBSD' => 'openbsd',
            'SunOS' => 'sunos',
            'Ubuntu' => 'ubuntu',
            'Linux (or Linux based)' => '(linux)|(x11)',
            'QNX' => 'QNX',
            'BeOS' => 'beos',
            'OS2' => 'os/2',
            'SearchBot' => '(nuhk)|(googlebot)|(yammybot)|(openbot)|(slurp)|(msnbot)|(ask jeeves/teoma)|(ia_archiver)'
        );

        $u_agent_lower = strtolower($u_agent);

        foreach ($osList as $os => $match) {
            if (preg_match('#' . $match . '#i', $u_agent_lower)) {
                $operatingSystem = $os;
                break;
            }
        }

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $u_agent)) {
                $browser = $value;
            }
        }

        $bname = '';
        $ub = "";

        $browser_array = array(
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        );

        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $u_agent)) {
                $bname = $value;
                $ub = $value;
            }
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);

        // Test
//    $f = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/ru/ru-en/backend/config/f.json"), true);
//    $f[] = [$u_agent, $matches];
//    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/ru/ru-en/backend/config/f.json", json_encode($f));


        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0] ?? $version;
            } else {
                $version = $matches['version'][1] ?? $version;
            }
        } else {
            $version = $matches['version'][0] ?? $version;
        }
    }

    return array(
        'name' => "{$bname} {$version}",
        'operatingSystem' => $operatingSystem
    );
}