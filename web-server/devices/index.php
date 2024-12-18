<?php
// get the user id parameter input (device_name_input)
$device_name_input = $_GET['id'] ?? null;

// check if the device name is provided
if (!$device_name_input) {
    die("No device name provided in the URL.");
}

// load the AsyncAPI JSON file
//$asyncapiFile = 'dht22-sensor-mqtt-protocol.json';
// database connection settings
$host = 'db';
$dbname = 'web_of_things';
$username = 'wot_user';
$password = 'web_of_things_mysql_db@';

try {
    // establish a database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare and execute the query with a placeholder for the device name
    $query = "SELECT * FROM `thing_description` WHERE device_name = :device_name";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['device_name' => $device_name_input]);

    // fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // check if data was fetched successfully
    if ($result && isset($result['td'])) {  // replace 'json_data_column' with the actual column name holding the JSON
        $asyncapiData = json_decode($result['td'], true);
    } else {
        die("No data found for the specified device.");
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
//$asyncapiData = json_decode(file_get_contents($asyncapiFile), true);

// check if JSON loaded successfully
if ($asyncapiData === null) {
    die("Failed to parse AsyncAPI JSON file.");
}

// extract required data
$apiTitle = $asyncapiData['info']['title'] ?? 'Unknown Title';
$apiProtocol = $asyncapiData['servers']['mqttBroker']['protocol'] ?? $asyncapiData['servers']['scram-connections']['protocol'] ?? 'Unknown Protocol';
$apiDescription = $asyncapiData['info']['description'] ?? 'No Description';
$humidityProperty = $asyncapiData['channels']['humidityMeasured']['description'] ?? 'Humidity data not available';
$temperatureProperty = $asyncapiData['channels']['temperatureMeasured']['description'] ?? 'Temperature data not available';

if ($device_name_input === "dht22-sensor-mqtt-protocol") {
    $device_image = 'dht22-temperature-sensor.png';
    $APIkeyword_1 = 'sensors';
    $APIkeyword_2 = 'temperature';
    $APIkeyword_3 = 'humidity';
} else if ($device_name_input === "streetlights-kafka-protocol") {
    $device_image = 'streetlight.webp';
    $APIkeyword_1 = 'actuators';
    $APIkeyword_2 = 'turn on';
    $APIkeyword_3 = 'turn off';
}

// prepare the AsyncAPI information to insert into the HTML
$thing_description = "<pre>" . json_encode($asyncapiData, JSON_PRETTY_PRINT) . "</pre>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="keywords" content="Web of Things, IoT devices, <?php echo $APIkeyword_1; ?>, <?php echo $APIkeyword_2; ?>, <?php echo $APIkeyword_3; ?>">
	<meta name="author" content="Aimilios Tzavaras">
	<meta name="robots" content="">
    <meta name="viewport" content="width=device-width,initial-scale=1">
	<meta name="description" content="<?php echo $apiTitle; ?>">
	<meta property="og:title" content="Zenix - Crypto Admin Dashboard">
	<meta property="og:description" content="Zenix - Crypto Admin Dashboard">
	<meta property="og:image" content="https://zenix.dexignzone.com/xhtml/social-image.png">
	<meta name="format-detection" content="telephone=no">
    <title><?php echo $apiTitle; ?> </title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
	<link rel="stylesheet" href="vendor/chartist/css/chartist.min.css">
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
	<link href="vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
	
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        <div class="nav-header">
            <a href="index.html" class="brand-logo">
                <svg class="logo-abbr" width="50" height="50" viewbox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect class="svg-logo-rect" width="50" height="50" rx="6" fill="#EB8153"></rect>
					<path class="svg-logo-path" d="M17.5158 25.8619L19.8088 25.2475L14.8746 11.1774C14.5189 9.84988 15.8701 9.0998 16.8205 9.75055L33.0924 22.2055C33.7045 22.5589 33.8512 24.0717 32.6444 24.3951L30.3514 25.0095L35.2856 39.0796C35.6973 40.1334 34.4431 41.2455 33.3397 40.5064L17.0678 28.0515C16.2057 27.2477 16.5504 26.1205 17.5158 25.8619ZM18.685 14.2955L22.2224 24.6007L29.4633 22.6605L18.685 14.2955ZM31.4751 35.9615L27.8171 25.6886L20.5762 27.6288L31.4751 35.9615Z" fill="white"></path>
				</svg>
                <svg class="brand-title" width="74" height="22" viewbox="0 0 74 22" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path class="svg-logo-path" d="M0.784 17.556L10.92 5.152H1.176V1.12H16.436V4.564L6.776 16.968H16.548V21H0.784V17.556ZM25.7399 21.28C24.0785 21.28 22.6599 20.9347 21.4839 20.244C20.3079 19.5533 19.4025 18.6387 18.7679 17.5C18.1519 16.3613 17.8439 15.1293 17.8439 13.804C17.8439 12.3853 18.1519 11.088 18.7679 9.912C19.3839 8.736 20.2799 7.79333 21.4559 7.084C22.6319 6.37467 24.0599 6.02 25.7399 6.02C27.4012 6.02 28.8199 6.37467 29.9959 7.084C31.1719 7.79333 32.0585 8.72667 32.6559 9.884C33.2719 11.0413 33.5799 12.2827 33.5799 13.608C33.5799 14.1493 33.5425 14.6253 33.4679 15.036H22.6039C22.6785 16.0253 23.0332 16.7813 23.6679 17.304C24.3212 17.808 25.0585 18.06 25.8799 18.06C26.5332 18.06 27.1585 17.9013 27.7559 17.584C28.3532 17.2667 28.7639 16.8373 28.9879 16.296L32.7959 17.36C32.2172 18.5173 31.3119 19.46 30.0799 20.188C28.8665 20.916 27.4199 21.28 25.7399 21.28ZM22.4919 12.292H28.8759C28.7825 11.3587 28.4372 10.6213 27.8399 10.08C27.2612 9.52 26.5425 9.24 25.6839 9.24C24.8252 9.24 24.0972 9.52 23.4999 10.08C22.9212 10.64 22.5852 11.3773 22.4919 12.292ZM49.7783 21H45.2983V12.74C45.2983 11.7693 45.1116 11.0693 44.7383 10.64C44.3836 10.192 43.9076 9.968 43.3103 9.968C42.6943 9.968 42.069 10.2107 41.4343 10.696C40.7996 11.1813 40.3516 11.8067 40.0903 12.572V21H35.6103V6.3H39.6423V8.764C40.1836 7.90533 40.949 7.23333 41.9383 6.748C42.9276 6.26267 44.0663 6.02 45.3543 6.02C46.3063 6.02 47.0716 6.19733 47.6503 6.552C48.2476 6.888 48.6956 7.336 48.9943 7.896C49.3116 8.43733 49.517 9.03467 49.6103 9.688C49.7223 10.3413 49.7783 10.976 49.7783 11.592V21ZM52.7548 4.62V0.559999H57.2348V4.62H52.7548ZM52.7548 21V6.3H57.2348V21H52.7548ZM63.4657 6.3L66.0697 10.444L66.3497 10.976L66.6297 10.444L69.2337 6.3H73.8537L68.9257 13.608L73.9657 21H69.3457L66.6017 16.884L66.3497 16.352L66.0977 16.884L63.3537 21H58.7337L63.7737 13.692L58.8457 6.3H63.4657Z" fill="black"></path>
				</svg>
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
		
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="input-group search-area right d-lg-inline-flex d-none">
								<input type="text" class="form-control" placeholder="Find something here...">
								<div class="input-group-append">
									<span class="input-group-text">
										<a href="javascript:void(0)">
											<i class="flaticon-381-search-2"></i>
										</a>
									</span>
								</div>
							</div>
                        </div>
                        <ul class="navbar-nav header-right main-notification">
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-theme-mode" href="#">
									<i id="icon-light" class="fa fa-sun-o"></i>
                                    <i id="icon-dark" class="fa fa-moon-o"></i>
                                </a>
							</li>
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-fullscreen" href="#">
                                    <svg id="icon-full" viewbox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3" style="stroke-dasharray: 37, 57; stroke-dashoffset: 0;"></path></svg>
                                    <svg id="icon-minimize" width="20" height="20" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minimize"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3" style="stroke-dasharray: 37, 57; stroke-dashoffset: 0;"></path></svg>
                                </a>
							</li>
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-toggle="dropdown">
                                   <svg class="bell-icon" width="24" height="24" viewbox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M22.75 15.8385V13.0463C22.7471 10.8855 21.9385 8.80353 20.4821 7.20735C19.0258 5.61116 17.0264 4.61555 14.875 4.41516V2.625C14.875 2.39294 14.7828 2.17038 14.6187 2.00628C14.4546 1.84219 14.2321 1.75 14 1.75C13.7679 1.75 13.5454 1.84219 13.3813 2.00628C13.2172 2.17038 13.125 2.39294 13.125 2.625V4.41534C10.9736 4.61572 8.97429 5.61131 7.51794 7.20746C6.06159 8.80361 5.25291 10.8855 5.25 13.0463V15.8383C4.26257 16.0412 3.37529 16.5784 2.73774 17.3593C2.10019 18.1401 1.75134 19.1169 1.75 20.125C1.75076 20.821 2.02757 21.4882 2.51969 21.9803C3.01181 22.4724 3.67904 22.7492 4.375 22.75H9.71346C9.91521 23.738 10.452 24.6259 11.2331 25.2636C12.0142 25.9013 12.9916 26.2497 14 26.2497C15.0084 26.2497 15.9858 25.9013 16.7669 25.2636C17.548 24.6259 18.0848 23.738 18.2865 22.75H23.625C24.321 22.7492 24.9882 22.4724 25.4803 21.9803C25.9724 21.4882 26.2492 20.821 26.25 20.125C26.2486 19.117 25.8998 18.1402 25.2622 17.3594C24.6247 16.5786 23.7374 16.0414 22.75 15.8385ZM7 13.0463C7.00232 11.2113 7.73226 9.45223 9.02974 8.15474C10.3272 6.85726 12.0863 6.12732 13.9212 6.125H14.0788C15.9137 6.12732 17.6728 6.85726 18.9703 8.15474C20.2677 9.45223 20.9977 11.2113 21 13.0463V15.75H7V13.0463ZM14 24.5C13.4589 24.4983 12.9316 24.3292 12.4905 24.0159C12.0493 23.7026 11.716 23.2604 11.5363 22.75H16.4637C16.284 23.2604 15.9507 23.7026 15.5095 24.0159C15.0684 24.3292 14.5411 24.4983 14 24.5ZM23.625 21H4.375C4.14298 20.9999 3.9205 20.9076 3.75644 20.7436C3.59237 20.5795 3.50014 20.357 3.5 20.125C3.50076 19.429 3.77757 18.7618 4.26969 18.2697C4.76181 17.7776 5.42904 17.5008 6.125 17.5H21.875C22.571 17.5008 23.2382 17.7776 23.7303 18.2697C24.2224 18.7618 24.4992 19.429 24.5 20.125C24.4999 20.357 24.4076 20.5795 24.2436 20.7436C24.0795 20.9076 23.857 20.9999 23.625 21Z" fill="#EB8153"></path>
									</svg>
									<div class="pulse-css"></div>
                                </a>
								<div class="dropdown-menu dropdown-menu-right">
                                    <div id="dlab_W_Notification1" class="widget-media dz-scroll p-3 height380">
										<ul class="timeline">
											<li>
												<div class="timeline-panel">
													<div class="media mr-2">
														<img alt="image" width="50" src="images/avatar/1.jpg">
													</div>
													<div class="media-body">
														<h6 class="mb-1">Dr sultads Send you Photo</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media mr-2 media-info">
														KG
													</div>
													<div class="media-body">
														<h6 class="mb-1">Resport created successfully</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media mr-2 media-success">
														<i class="fa fa-home"></i>
													</div>
													<div class="media-body">
														<h6 class="mb-1">Reminder : Treatment Time!</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
											 <li>
												<div class="timeline-panel">
													<div class="media mr-2">
														<img alt="image" width="50" src="images/avatar/1.jpg">
													</div>
													<div class="media-body">
														<h6 class="mb-1">Dr sultads Send you Photo</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media mr-2 media-danger">
														KG
													</div>
													<div class="media-body">
														<h6 class="mb-1">Resport created successfully</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
											<li>
												<div class="timeline-panel">
													<div class="media mr-2 media-primary">
														<i class="fa fa-home"></i>
													</div>
													<div class="media-body">
														<h6 class="mb-1">Reminder : Treatment Time!</h6>
														<small class="d-block">29 July 2020 - 02:26 PM</small>
													</div>
												</div>
											</li>
										</ul>
									</div>
                                    <a class="all-notification" href="javascript:void(0)">See all notifications <i class="ti-arrow-right"></i></a>
                                </div>
							</li>
							<li class="nav-item dropdown notification_dropdown d-sm-flex d-none">
                                <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-toggle="dropdown">
                                  <svg width="24" height="24" viewbox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M23.625 6.12506H22.75V2.62506C22.75 2.47268 22.7102 2.32295 22.6345 2.19068C22.5589 2.05841 22.45 1.94819 22.3186 1.87093C22.1873 1.79367 22.0381 1.75205 21.8857 1.75019C21.7333 1.74832 21.5831 1.78629 21.4499 1.86031L14 5.99915L6.55007 1.86031C6.41688 1.78629 6.26667 1.74832 6.11431 1.75019C5.96194 1.75205 5.8127 1.79367 5.68136 1.87093C5.55002 1.94819 5.44113 2.05841 5.36547 2.19068C5.28981 2.32295 5.25001 2.47268 5.25 2.62506V6.12506H4.375C3.67904 6.12582 3.01181 6.40263 2.51969 6.89475C2.02757 7.38687 1.75076 8.0541 1.75 8.75006V11.3751C1.75076 12.071 2.02757 12.7383 2.51969 13.2304C3.01181 13.7225 3.67904 13.9993 4.375 14.0001H5.25V23.6251C5.25076 24.321 5.52757 24.9882 6.01969 25.4804C6.51181 25.9725 7.17904 26.2493 7.875 26.2501H20.125C20.821 26.2493 21.4882 25.9725 21.9803 25.4804C22.4724 24.9882 22.7492 24.321 22.75 23.6251V14.0001H23.625C24.321 13.9993 24.9882 13.7225 25.4803 13.2304C25.9724 12.7383 26.2492 12.071 26.25 11.3751V8.75006C26.2492 8.0541 25.9724 7.38687 25.4803 6.89475C24.9882 6.40263 24.321 6.12582 23.625 6.12506ZM21 6.12506H17.3769L21 4.11256V6.12506ZM7 4.11256L10.6231 6.12506H7V4.11256ZM7 23.6251V14.0001H13.125V24.5001H7.875C7.64303 24.4998 7.42064 24.4075 7.25661 24.2434C7.09258 24.0794 7.0003 23.857 7 23.6251ZM21 23.6251C20.9997 23.857 20.9074 24.0794 20.7434 24.2434C20.5794 24.4075 20.357 24.4998 20.125 24.5001H14.875V14.0001H21V23.6251ZM24.5 11.3751C24.4997 11.607 24.4074 11.8294 24.2434 11.9934C24.0794 12.1575 23.857 12.2498 23.625 12.2501H4.375C4.14303 12.2498 3.92064 12.1575 3.75661 11.9934C3.59258 11.8294 3.5003 11.607 3.5 11.3751V8.75006C3.5003 8.51809 3.59258 8.2957 3.75661 8.13167C3.92064 7.96764 4.14303 7.87536 4.375 7.87506H23.625C23.857 7.87536 24.0794 7.96764 24.2434 8.13167C24.4074 8.2957 24.4997 8.51809 24.5 8.75006V11.3751Z" fill="#EB8153"></path>
									</svg>
                                </a>
								<div class="dropdown-menu dropdown-menu-right p-3">
									<div id="DZ_W_TimeLine11" class="widget-timeline dz-scroll style-1 height370">
										<ul class="timeline">
											<li>
												<div class="timeline-badge primary"></div>
												<a class="timeline-panel text-muted" href="#">
													<span>10 minutes ago</span>
													<h6 class="mb-0">Youtube, a video-sharing website, goes live <strong class="text-primary">$500</strong>.</h6>
												</a>
											</li>
											<li>
												<div class="timeline-badge info">
												</div>
												<a class="timeline-panel text-muted" href="#">
													<span>20 minutes ago</span>
													<h6 class="mb-0">New order placed <strong class="text-info">#XF-2356.</strong></h6>
													<p class="mb-0">Quisque a consequat ante Sit amet magna at volutapt...</p>
												</a>
											</li>
											<li>
												<div class="timeline-badge danger">
												</div>
												<a class="timeline-panel text-muted" href="#">
													<span>30 minutes ago</span>
													<h6 class="mb-0">john just buy your product <strong class="text-warning">Sell $250</strong></h6>
												</a>
											</li>
											<li>
												<div class="timeline-badge success">
												</div>
												<a class="timeline-panel text-muted" href="#">
													<span>15 minutes ago</span>
													<h6 class="mb-0">StumbleUpon is acquired by eBay. </h6>
												</a>
											</li>
											<li>
												<div class="timeline-badge warning">
												</div>
												<a class="timeline-panel text-muted" href="#">
													<span>20 minutes ago</span>
													<h6 class="mb-0">Mashable, a news website and blog, goes live.</h6>
												</a>
											</li>
											<li>
												<div class="timeline-badge dark">
												</div>
												<a class="timeline-panel text-muted" href="#">
													<span>20 minutes ago</span>
													<h6 class="mb-0">Mashable, a news website and blog, goes live.</h6>
												</a>
											</li>
										</ul>
									</div>
								</div>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                                    <img src="images/profile/pic1.jpg" width="20" alt="">
									<div class="header-info">
										<span>Johndoe</span>
										<small>Super Admin</small>
									</div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="app-profile.html" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ml-2">Profile </span>
                                    </a>
                                    <a href="email-inbox.html" class="dropdown-item ai-icon">
                                        <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                        <span class="ml-2">Inbox </span>
                                    </a>
                                    <a href="page-login.html" class="dropdown-item ai-icon">
                                        <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                        <span class="ml-2">Logout </span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
				<div class="sub-header">
					<div class="d-flex align-items-center flex-wrap mr-auto">
						<h5 class="dashboard_bar">Dashboard</h5>
					</div>
					<div class="d-flex align-items-center">
						<a href="javascript:void(0);" class="btn btn-xs btn-primary light mr-1">Today</a>
						<a href="javascript:void(0);" class="btn btn-xs btn-primary light mr-1">Month</a>
						<a href="javascript:void(0);" class="btn btn-xs btn-primary light">Year</a>
					</div>
				</div>
			</div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="deznav">
            <div class="deznav-scroll">
				<div class="main-profile">
					<div class="image-bx">
						<img src="images/TUC_logo.png" alt="">
						<a href="javascript:void(0);"><i class="fa fa-cog" aria-hidden="true"></i></a>
					</div>
					<h5 class="name"><span class="font-w400">Hello,</span> Aimilios</h5>
					<p class="email"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="88e5e9faf9fdedf2f2f2f2c8e5e9e1e4a6ebe7e5">atzavaras@tuc.gr</a></p>
				</div>
				<ul class="metismenu" id="menu">
					<li class="nav-label first">Main Menu</li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-144-layout"></i>
							<span class="nav-text">Dashboard</span>
						</a>
                        <ul aria-expanded="false">
							<li><a href="index.html">Dashboard Light</a></li>
							<li><a href="index-2.html">Dashboard Dark</a></li>
							<li><a href="my-wallets.html">Wallet</a></li>
							<li><a href="tranasactions.html">Transactions</a></li>
							<li><a href="coin-details.html">Coin Details</a></li>
							<li><a href="portofolio.html">Portofolio</a></li>
							<li><a href="market-capital.html">Market Capital</a></li>
						</ul>

                    </li>
					<li class="nav-label">Apps</li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
						<i class="flaticon-077-menu-1"></i>
							<span class="nav-text">Apps</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="app-profile.html">Profile</a></li>
							<li><a href="post-details.html">Post Details</a></li>
							<li><a href="page-chat.html">Chat<span class="badge badge-xs badge-danger">New</span></a></li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">Project<span class="badge badge-xs badge-danger">New</span></a>
                                <ul aria-expanded="false">
                                    <li><a href="project-list.html">Project List</a></li>
                                    <li><a href="project-card.html">Project Card</a></li>
                                </ul>
                            </li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">User<span class="badge badge-xs badge-danger">New</span></a>
                                <ul aria-expanded="false">
                                    <li><a href="user-list-datatable.html">User List</a></li>
                                    <li><a href="user-list-column.html">User Card</a></li>
                                </ul>
                            </li>
							<li><a class="has-arrow" href="javascript:void(0);" aria-expanded="false">Contact<span class="badge badge-xs badge-danger">New</span></a>
                                <ul aria-expanded="false">
                                    <li><a href="contact-list.html">Contact List</a></li>
                                    <li><a href="contact-card.html">Contact Card</a></li>
                                </ul>
                            </li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Email</a>
                                <ul aria-expanded="false">
                                    <li><a href="email-compose.html">Compose</a></li>
                                    <li><a href="email-inbox.html">Inbox</a></li>
                                    <li><a href="email-read.html">Read</a></li>
                                </ul>
                            </li>
                            <li><a href="app-calender.html">Calendar</a></li>
							<li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Shop</a>
                                <ul aria-expanded="false">
                                    <li><a href="ecom-product-grid.html">Product Grid</a></li>
									<li><a href="ecom-product-list.html">Product List</a></li>
									<li><a href="ecom-product-detail.html">Product Details</a></li>
									<li><a href="ecom-product-order.html">Order</a></li>
									<li><a href="ecom-checkout.html">Checkout</a></li>
									<li><a href="ecom-invoice.html">Invoice</a></li>
									<li><a href="ecom-customers.html">Customers</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
					
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-061-puzzle"></i>
							<span class="nav-text">Charts</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="chart-flot.html">Flot</a></li>
                            <li><a href="chart-morris.html">Morris</a></li>
                            <li><a href="chart-chartjs.html">Chartjs</a></li>
                            <li><a href="chart-chartist.html">Chartist</a></li>
                            <li><a href="chart-sparkline.html">Sparkline</a></li>
                            <li><a href="chart-peity.html">Peity</a></li>
                        </ul>
                    </li>
					<li class="nav-label">components</li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-003-diamond"></i>
							<span class="nav-text">Bootstrap</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="ui-accordion.html">Accordion</a></li>
                            <li><a href="ui-alert.html">Alert</a></li>
                            <li><a href="ui-badge.html">Badge</a></li>
                            <li><a href="ui-button.html">Button</a></li>
                            <li><a href="ui-modal.html">Modal</a></li>
                            <li><a href="ui-button-group.html">Button Group</a></li>
                            <li><a href="ui-list-group.html">List Group</a></li>
                            <li><a href="ui-media-object.html">Media Object</a></li>
                            <li><a href="ui-card.html">Cards</a></li>
                            <li><a href="ui-carousel.html">Carousel</a></li>
                            <li><a href="ui-dropdown.html">Dropdown</a></li>
                            <li><a href="ui-popover.html">Popover</a></li>
                            <li><a href="ui-progressbar.html">Progressbar</a></li>
                            <li><a href="ui-tab.html">Tab</a></li>
                            <li><a href="ui-typography.html">Typography</a></li>
                            <li><a href="ui-pagination.html">Pagination</a></li>
                            <li><a href="ui-grid.html">Grid</a></li>

                        </ul>
                    </li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-053-heart"></i>
							<span class="nav-text">Plugins</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="uc-select2.html">Select 2</a></li>
                            <li><a href="uc-nestable.html">Nestedable</a></li>
                            <li><a href="uc-noui-slider.html">Noui Slider</a></li>
                            <li><a href="uc-sweetalert.html">Sweet Alert</a></li>
                            <li><a href="uc-toastr.html">Toastr</a></li>
                            <li><a href="map-jqvmap.html">Jqv Map</a></li>
							<li><a href="uc-lightgallery.html">Light Gallery</a></li>
                        </ul>
                    </li>
                    <li><a href="widget-basic.html" class="ai-icon" aria-expanded="false">
							<i class="flaticon-381-settings-2"></i>
							<span class="nav-text">Widget</span>
						</a>
					</li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-044-file"></i>
							<span class="nav-text">Forms</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="form-element.html">Form Elements</a></li>
                            <li><a href="form-wizard.html">Wizard</a></li>
                            <li><a href="form-editor-summernote.html">Summernote</a></li>
                            <li><a href="form-pickers.html">Pickers</a></li>
                            <li><a href="form-validation-jquery.html">Jquery Validate</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-381-network"></i>
							<span class="nav-text">Table</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="table-bootstrap-basic.html">Bootstrap</a></li>
                            <li><a href="table-datatable-basic.html">Datatable</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
							<i class="flaticon-049-copy"></i>
							<span class="nav-text">Pages</span>
						</a>
                        <ul aria-expanded="false">
                            <li><a href="page-register.html">Register</a></li>
                            <li><a href="page-login.html">Login</a></li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Error</a>
                                <ul aria-expanded="false">
                                    <li><a href="page-error-400.html">Error 400</a></li>
                                    <li><a href="page-error-403.html">Error 403</a></li>
                                    <li><a href="page-error-404.html">Error 404</a></li>
                                    <li><a href="page-error-500.html">Error 500</a></li>
                                    <li><a href="page-error-503.html">Error 503</a></li>
                                </ul>
                            </li>
                            <li><a href="page-lock-screen.html">Lock Screen</a></li>
                        </ul>
                    </li>
                </ul>
				<div class="copyright">
					<p><strong>Zenix Crypto Admin Dashboard</strong> © 2021 All Rights Reserved</p>
					<p class="fs-12">Made with <span class="heart"></span> by DexignZone</p>
				</div>
			</div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xl-12">
						<div class="card">
							<div class="card-body d-flex justify-content-between align-items-center">
								<div>
									<h4>IoT device (Thing) API information</h4>
									<span>Lorem ipsum sit amet</span>
								</div>
								<a href="javascript:void(0);" class="btn btn-info light">+ Add Card</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/devices/<?php echo $device_image; ?>" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);"><?php echo $apiTitle; ?></a></h5>
										<span class="text-primary"><?php echo $apiProtocol; ?> protocol</span>
									</div>
								</div>
								<p class="fs-14"><?php echo $apiDescription; ?></p>
                                <br><p class="fs-13"><?php echo $thing_description; ?></p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2">exa<a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="cea3bea2ab8ea9a3afa7a2e0ada1a3">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-success btn-xs">Write Message</a>
							</div>
						</div>
					</div>
                    <!---
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3 rounded-circle">
										<img src="images/users/pic2.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Oliver Jean</a></h5>
										<span class="text-info">Junior Developer</span>
									</div>
								</div>
								<p class="fs-12">Maintain inventory of supplies and order new stock as needed Maintain inventory stock</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="8aeff2ebe7fae6efcaede7ebe3e6a4e9e5e7">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-primary btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<span class="icon-placeholder bg-primary text-white">pm</span>
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Post Melone</a></h5>
										<span class="text-success">Senior Designer</span>
									</div>
								</div>
								<p class="fs-12">Anticipate guests needs in order to accommodate them and provide an exceptional guest experience</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1b7e637a766b777e5b7c767a727735787476">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-secondary btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media rounded-circle mr-3">
										<span class="icon-placeholder bgl-success text-success">km</span>
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Kevin Mandala</a></h5>
										<span class="text-danger">Junior Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1d78657c706d71785d7a707c7471337e7270">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-info btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic3.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Mc. Kowalski</a></h5>
										<span class="text-info">Php Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="adc8d5ccc0ddc1c8edcac0ccc4c183cec2c0">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-info light btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic4.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Rio Fernandez</a></h5>
										<span class="text-danger">Python Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d8bda0b9b5a8b4bd98bfb5b9b1b4f6bbb7b5">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-success btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic5.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Chintya Laudia</a></h5>
										<span class="text-warning">NodeJs Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="04617c6569746861446369656d682a676b69">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-warning light btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic6.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">James Junaidi</a></h5>
										<span class="text-primary">Senior Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5b3e233a362b373e1b3c363a323775383436">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-primary light btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic7.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Keanu Repes</a></h5>
										<span class="text-primary">Senior Designer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d5b0adb4b8a5b9b095b2b8b4bcb9fbb6bab8">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-outline-danger btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<img src="images/users/pic8.jpg" alt="">
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Tonni Sblak</a></h5>
										<span class="text-primary">Senior Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d2b7aab3bfa2beb792b5bfb3bbbefcb1bdbf">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-outline-success btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<span class="icon-placeholder bg-primary text-white">jk</span>
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">John Kipli</a></h5>
										<span class="text-primary">Senior Developer</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="80e5f8e1edf0ece5c0e7ede1e9ecaee3efed">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-outline-warning btn-xs">Write Message</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-xxl-4 col-sm-6">
						<div class="card user-card">
							<div class="card-body pb-0">
								<div class="d-flex mb-3 align-items-center">
									<div class="dz-media mr-3">
										<span class="icon-placeholder bg-primary text-white">mo</span>
									</div>
									<div>
										<h5 class="title"><a href="javascript:void(0);">Monalisa</a></h5>
										<span class="text-primary">Senior Head</span>
									</div>
								</div>
								<p class="fs-12">Answering guest inquiries, directing phone calls, coordinating travel plans, and more.</p>
								<ul class="list-group list-group-flush">
									<li class="list-group-item">
										<span class="mb-0 title">Email</span> :
										<span class="text-black ml-2"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="75100d1418051910351218141c195b161a18">[email&#160;protected]</a></span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Phone</span> :
										<span class="text-black ml-2">1238545644</span>
									</li>
									<li class="list-group-item">
										<span class="mb-0 title">Location</span> :
										<span class="text-black desc-text ml-2">Indonasia</span>
									</li>
								</ul>
							</div>
							<div class="card-footer">
								<a href="javascript:void(0);" class="btn btn-outline-info btn-xs">Write Message</a>
							</div>
						</div>
					</div>---> 
				</div>
				<nav>
					<ul class="pagination pagination-gutter pagination-primary no-bg">
						<li class="page-item page-indicator">
							<a class="page-link" href="javascript:void(0)">
								<i class="la la-angle-left"></i></a>
						</li>
						<li class="page-item "><a class="page-link" href="javascript:void(0)">1</a>
						</li>
						<li class="page-item active"><a class="page-link" href="javascript:void(0)">2</a></li>
						<li class="page-item"><a class="page-link" href="javascript:void(0)">3</a></li>
						<li class="page-item"><a class="page-link" href="javascript:void(0)">4</a></li>
						<li class="page-item page-indicator">
							<a class="page-link" href="javascript:void(0)">
								<i class="la la-angle-right"></i></a>
						</li>
					</ul>
				</nav>
			</div>
		</div>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="../index.htm" target="_blank">DexignZone</a> 2021</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
		
		
		
		
		
		<!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="vendor/global/global.min.js"></script>
	<script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
	<script src="vendor/chart.js/Chart.bundle.min.js"></script>
	
	<!-- Datatable -->
	<script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
	<script src="js/plugins-init/datatables.init.js"></script>
	
    <script src="js/custom.min.js"></script>
	<script src="js/deznav-init.js"></script>
    <script src="js/demo.js"></script>
    <script src="js/styleSwitcher.js"></script>

	<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Thing",
        "name": "Web of Things - DHT22 Temperature Sensor - MQTT API",
        "description": "The API of a sensor that measures temperature and humidity, described by AsyncAPI TD. Aimilios Tzavaras. Technical University of Crete.",
        "keywords": "sensor, WoT, temperature",
		"property": [
            {
                "@type": "PropertyValue",
                "name": "Temperature",
                "value": "23.5",
                "unitCode": "CEL"
            },
            {
                "@type": "PropertyValue",
                "name": "Humidity",
                "value": "60",
                "unitCode": "PERCENT"
            }
        ]
    }
    </script>

</body>
</html>