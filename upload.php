<?php
//this uploads and converts the video, should switch to a better solution!
require($_SERVER['DOCUMENT_ROOT'] . '/lib/common.php');
use Intervention\Image\ImageManager;
use Streaming\FFMpeg;
use FFMpeg\Coordinate;
use FFMpeg\Media;
use FFMpeg\Filters;

$manager = new ImageManager();

$video_id = md5(bin2hex(random_bytes(6)));
$new = '';
foreach(str_split($video_id) as $char){
	if (rand(0, 1) == 1) {
		$char = str_rot13($char);
	} else if (rand(0, 2) == 2) {
		$char = '_';
	} else if (rand(0, 3) == 3) {
		$char = mb_strtoupper($char);
	} else if (rand(0, 4) == 4) {
		$char = '-';
	}
	$new .= $char;
}
$new = substr($new, 0, 11);

if(isset($_POST['upload']) AND isset($currentUser['username'])){
	$name       = $_FILES['fileToUpload']['name'];  
    $temp_name  = $_FILES['fileToUpload']['tmp_name'];  // gets video info and thumbnail info
	$ext  = pathinfo( $_FILES['fileToUpload']['name'], PATHINFO_EXTENSION );
	$target_file = $_SERVER['DOCUMENT_ROOT'] . '/videos/'.$new.'.'.$ext;
	if(move_uploaded_file($temp_name, $target_file)){
		$config = [
			'timeout'          => 3600, // The timeout for the underlying process
			'ffmpeg.threads'   => 12,   // The number of threads that FFmpeg should use
			'ffmpeg.binaries'  => $ffmpegPath,
			'ffprobe.binaries' => $ffprobePath,
		];
		try {
			query("INSERT INTO videos (video_id, title, description, author, time) VALUES (?,?,?,?,?)",
				[$new,$_POST['title'],$_POST['desc'],$currentUser['id'],time()]);
			redirect('./watch.php?v='.$new);
		} catch (Exception $e) {
			echo '<p>Something went wrong!:'.htmlspecialchars($e->getMessage()).'on line:'.htmlspecialchars($e->getLine()).'</p> <p>stack trace:'.htmlspecialchars($e->getTraceAsString()).'</p>';
			foreach (glob("videos/$new*") as $filename) {
			   unlink($filename);
			}
		}
	}
}

$twig = twigloader();
echo $twig->render('upload.twig');
?>