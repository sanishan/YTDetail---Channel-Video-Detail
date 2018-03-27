<?php
require_once('config.php');
$video_id=array();
$video_detail=array();
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
); 
if ($_POST) {
    if (isset($_POST['ytc']) && $_POST['ytc']!='') {
$content=file_get_contents("https://www.googleapis.com/youtube/v3/search?key=$youtube_api&channelId=".$_POST['ytc']."&part=snippet,id&order=date&maxResults=$result_count", false, stream_context_create($arrContextOptions));

$json=json_decode($content,true);
  if (isset($json) && $json!=''){
foreach ($json['items'] as $videos){
        
        $video_id[]=$videos['id']['videoId'];
}

$video_id_list = implode(',', $video_id);
    
unset($json);
    if (isset($video_id_list) && $video_id_list!=''){
    $request2 = "https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics&id=$video_id_list&key=$youtube_api";
    $request=file_get_contents($request2, false, stream_context_create($arrContextOptions));

    $json=json_decode($request,true);
     if (isset($_POST['ytd']) && $_POST['ytd']!='' && is_numeric($_POST['ytd']))  {
         $sec_lenght=$_POST['ytd'];
     } else {
         $sec_lenght=1500;
     }
       $pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i";
    $replacement = "";
    foreach ($json['items'] as $vd){
       // $vid_id['id'] = $vd['id'];
       // $vid_id['sec'] = ;

    $decs=preg_replace($pattern, $replacement, $vd['snippet']['description']);
       if (timeinsec(covtime($vd['contentDetails']['duration']))>=$sec_lenght){
        $video_detail[]=array('id'=>$vd['id'],
        'sec'=>timeinsec(covtime($vd['contentDetails']['duration'])),
        'time'=>covtime($vd['contentDetails']['duration']),
        'title'=>$vd['snippet']['title'],
        'decs'=>$decs,
        'tags'=>implode(',',$vd['snippet']['tags']),
        
        );
       }
          }
    }
}
    }
}
//print_a($video_detail);

?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title;?></title>
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link href="https://www.jqueryscript.net/demo/Exporting-Html-Tables-To-CSV-XLS-XLSX-Text-TableExport/dist/css/tableexport.css" rel="stylesheet" type="text/css">
<style>
body { background-color:#fafafa; font-family:'Roboto';}
.container { margin:150px auto;}
</style>
</head>

<body>
<div class="container">
 <form name="ytchannel" method="post" action="">
 Youtube Channel ID: <input type="text" class="form-control"  name="ytc"><br>
 Video Lenght in Seconds: <input type="text" class="form-control"  name="ytd" value="1500">
 Filter any video is less than X seconds<br>
 Note: We scrape maximum 50 Videos from a channel (No Pagination)!<br>
 <input type="submit" name="Submit" value="Video List">
 </form>


<?php
if (isset($video_detail) && is_array($video_detail) && !empty($video_detail)){
echo '<h3>All Links:

<!-- Trigger -->
<button class="btn btn-sm" data-clipboard-action="copy" data-clipboard-target="#allinks">
    Copy links to clipboard
</button></h3><textarea id="allinks" class="form-control" rows="10" cols="120">';
foreach ($video_detail as $print){
    echo 'https://www.youtube.com/watch?v='.$print['id']."\n";
}
echo '</textarea>';
?>

<table width="100%" valign="TOP" class="table table-striped">
   <thead><tr>
      <th>
         Title
      </th>
      <th>
         Description
      </th>
      <th>
         Tags
      </th>
      <th>
         Duration
      </th>
      <th>
         Links
      </th>
   </tr>
   </thead>
   <tbody>
<?php
 foreach ($video_detail as $print){
?>

   <tr>
      <td valign="TOP">
        <?php echo $print['title'];?>
      </td>
      <td valign="TOP">
         <?php echo $print['decs'];?>
      </td>
      <td valign="TOP">
         <?php echo $print['tags'];?>
      </td>
      <td valign="TOP">
         <?php echo $print['time'];?>
      </td>
      <td valign="TOP">
         <?php echo 'https://youtu.be/'.$print['id'];?>
      </td>
   </tr>

  <?php
 }
  ?>
  </tbody>
    </table>

    
<?php
 foreach ($video_detail as $print){
?>

<h3>All Links with Detail:</h3>
<b>Video Title:</b> <a style="cursor: pointer" data-clipboard-action="copy" class="btn"  data-clipboard-target="#title_<?php echo $print['id'];?>">Copy Title</a> 
<br>
      <input class="form-control" id="title_<?php echo $print['id'];?>" type="text" name="title" value="<?php echo $print['title'];?>">
 <br>
<b>Video Link:</b><a style="cursor: pointer" data-clipboard-action="copy" class="btn"  data-clipboard-target="#link_<?php echo $print['id'];?>">Copy Link</a>
 <br><input id="link_<?php echo $print['id'];?>" class="form-control" type="text" name="video" value="<?php echo 'https://youtu.be/'.$print['id'];?>"><br>
<b>Description: <a style="cursor: pointer" class="btn" data-clipboard-action="copy"  data-clipboard-target="#decs_<?php echo $print['id'];?>">Copy Description</a>
 </b><br><textarea class="form-control"  id="decs_<?php echo $print['id'];?>" name="decs" rows="10" cols="120"><?php echo $print['decs'];?></textarea>    <br>
<b>Tags:</b> <a style="cursor: pointer" class="btn" data-clipboard-action="copy"  data-clipboard-target="#tags_<?php echo $print['id'];?>">Copy Tags</a> 
<br><textarea class="form-control" name="tags"  id="tags_<?php echo $print['id'];?>" rows="5" cols="120"><?php echo $print['tags'];?></textarea>    <br>
<b>Duration:</b> <?php echo $print['time'];?>   <br>
<hr>
<hr>
  <?php
 }
}
  ?>
  
  </div> 
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://www.jqueryscript.net/demo/Exporting-Html-Tables-To-CSV-XLS-XLSX-Text-TableExport/FileSaver.min.js"></script>
<script src="https://www.jqueryscript.net/demo/Exporting-Html-Tables-To-CSV-XLS-XLSX-Text-TableExport/Blob.min.js"></script>
<script src="https://www.jqueryscript.net/demo/Exporting-Html-Tables-To-CSV-XLS-XLSX-Text-TableExport/xls.core.min.js"></script>

<script src="https://www.jqueryscript.net/demo/Exporting-Html-Tables-To-CSV-XLS-XLSX-Text-TableExport/dist/js/tableexport.js"></script>
<script src="clipboard.min.js"></script>
<script>
new ClipboardJS('.btn');
$("table").tableExport({formats: ["xlsx","xls", "csv", "txt"], bootstrap: true,    });
</script>
<footer>
<div class="container">
<span class="center">Powered by: <a href="http://mp3ora.com/" target="_blank">Mp3Ora.com</a></span>
</div>
</footer>
</body>
</html>