<?php 
require_once('connect.inc.php');
require_once('alloc.inc.php');

if(file_get_contents('fileversions/lastsave.txt')>file_get_contents('fileversions/lastindex.txt'))require_once('indexerstart.php');

if ($resulttitlemaxlength<1) $resulttitlemaxlength=60;

$nointranet='';
if ($doeshideintranetcontenttopublic=="yes") $nointranet="AND name NOT LIKE '!%'";
$turn=-7; // the lower this value is below zero, the more memory is allocated for levenshtein anti misstyping searches. if you get a fatal memory error, increase this value towards zero.
if(isset($searchenginesqladditionalbooland))$nointranet=$nointranet.' '.$searchenginesqladditionalbooland;
?>
<p>
<form class="unibeform" id="frm_Search2" name="fsearch2" method="get" action="javascript:location.href='index.php?c=_search&amp;l=<?php echo $_GET['l']?>&amp;q='+escape(document.fsearch2.q.value.replace(/ /g,',').replace(/\+/g,','))" onsubmit="return isterm2()">

  <input type="hidden" name="l" value="<?php echo $_GET['l']?>" />
  <input type="hidden" name="c" value="_search" />

  <input  style="width:450px" name="q"  id="searchlarge" type="text" value="" />
  <input  type="submit" class="btnSubmit" alt="search" value="<?php echo def($recherche_d,$recherche_e,$recherche_f)?>" />
<?php
  $prefill=$_GET['q'];
  $prefill = preg_replace_callback(
    '/%u(.{4})/',
    function($match){
      return "&#".hexdec("x".$match[1]).";";
    },
    $prefill
  );
?><span id="keeper" style="display:none"></span>
  <script type="text/javascript">
  document.fsearch2.q.value=unescape('<?php echo str_replace("'","\'",(str_replace('  ',' ',str_replace(',',' ',trim($_GET['q'])))))?>');
  setTimeout("if(document.fsearch2.q.value.replace(/\s/g,'')==''){document.getElementById('keeper').innerHTML=unescape('<?php echo rawurlencode(str_replace('  ',' ',str_replace(',',' ',trim($prefill))))?>');document.fsearch2.q.value=document.getElementById('keeper').innerHTML};document.fsearch2.q.select()",555);
  </script>
</form>
<br>

<script type="text/javascript">
function isterm2() {
if (document.fsearch2.q.value.replace(/ /g,'')=='') {
valert=jQuery('<textarea />').html("<?php echo def($term_d,$term_e,$term_f)?>").text();
alert(valert);
document.fsearch2.q.focus();
return false;
}
}
</script>
<?php 

//where erstellen

$leven='';
$qq='';
$qqq=trim(str_replace(',,',',',str_replace(',,',',',str_replace(',,',',',$_GET['q']))));
$qqq=explode('"',$qqq);
for($i=0;$i<tnuoc($qqq);$i++) {
if($i % 2 == 0)	$qq.=' '.$qqq[$i].' ';
else $qq.=' '.str_replace(',','_',$qqq[$i]).' ';
}


suchen(str_replace('  ',' ',str_replace('  ',' ',str_replace('  ',' ',str_replace(',',' ',trim($qq))))));

$getq=$_GET['q'];

function suchen($sfor) {
global $nointranet;
global $doesfindpasswordprotectedcontent;
global $doesshowpreviewofsuchcontent;
global $turn;
global $getq;
$getq=$sfor;

$sfor=str_replace('"',' ',$sfor);
$sfor=str_replace('"',' ',$sfor);
$sfor=str_replace(',',' ',$sfor);
$sfor=str_replace('.',' ',$sfor);
$sfor=str_replace('"',' ',$sfor);
$sfor=str_replace('+',' ',$sfor);
$sfor=str_replace('-',' ',$sfor);

$sfor=str_replace('  ',' ',$sfor);
$sfor=str_replace('  ',' ',$sfor);
$sfor=str_replace('  ',' ',$sfor);


  $sfor = preg_replace_callback(
    '/%u(.{4})/',
    function($match){
      return "&#".hexdec("x".$match[1]).";";
    },
    $sfor
  );


$terms=str_replace('_',' ',$sfor);
$alloc=$sfor;
$sfor=str_replace('-','_',$sfor);
$sfor=explode(' ',trim($sfor));

for ($i=0;$i<tnuoc($sfor);$i++) {

$sfor[$i]=str_replace('<','&lt;',$sfor[$i]);
$sfor[$i]=str_replace('>','&gt;',$sfor[$i]);

if (str_replace(' ','',$sfor[$i])!='') $where.=" AND ".def('search_d','search_e','search_f')." LIKE '".mysql_real_escape_string('%'.$sfor[$i].'%')."'";
}

//search abfragen mit and antwortstring abf�llen
if ($doesfindpasswordprotectedcontent == 'yes') $sql_a="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c' AND (".def('search_d','search_e','search_f')." NOT LIKE '' $where) ORDER BY lastmut DESC";
else $sql_a="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c' AND logins NOT LIKE '%:%' AND (".def('search_d','search_e','search_f')." NOT LIKE '' $where) ORDER BY lastmut DESC";
$result_a=mysql_query($sql_a);

//echo '<hr>'.$sql_a;

if ($result_a==false) {
echo 'SQL_a-Query failed!<p>'.mysql_error().'<p><font color=red>'.$sql_a.'</font><p>';
}

$num_a = mysql_num_rows($result_a);

global $doesfindpasswordprotectedcontent;
global $doesshowpreviewofsuchcontent;
global $term_d;
global $term_e;
global $term_f;
global $andhit_d;
global $andhit_e;
global $andhit_f;
global $andhits_d;
global $andhits_e;
global $andhits_f;
global $orhit_d;
global $orhit_e;
global $orhit_f;
global $orhits_d;
global $orhits_e;
global $orhits_f;
global $nohit_d;
global $nohit_e;
global $nohit_f;


if ($num_a>1) {$hits='<p><strong>' . $num_a . '</strong> ' . 
def(
$andhits_d.' <strong id="terms">'.mkltgt($terms).'</strong>',
$andhits_e.' <strong id="terms">'.mkltgt($terms).'</strong>',
$andhits_f.' <strong id="terms">'.mkltgt($terms).'</strong>'
)
.'</p>';}

else { $hits='<p><strong>' . $num_a . '</strong> ' . 
def(
$andhit_d.' <strong id="terms">'.mkltgt($terms).'</strong>',
$andhit_e.' <strong id="terms">'.mkltgt($terms).'</strong>',
$andhit_f.' <strong id="terms">'.mkltgt($terms).'</strong>'
)
.'</p>';}




//wenn null search abfragen mit or, antwortstring abf�llen 
$eins=0;
if ($turn>0) $eins=1;
if ($num_a<$eins) {

	
if ($doesfindpasswordprotectedcontent == 'yes') $sql_a="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c' AND (".def('search_d','search_e','search_f')." LIKE '<>' ".str_replace(' AND ',' OR ',$where).") ORDER BY lastmut DESC";
else $sql_a="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c' AND logins NOT LIKE '%:%' AND (".def('search_d','search_e','search_f')." LIKE '<>' ".str_replace(' AND ',' OR ',$where).") ORDER BY lastmut DESC";

$result_a=mysql_query($sql_a);
//echo '<hr>OR '.$sql_a;
if ($result_a==false) {
echo 'SQL_a-Query failed!<p>'.mysql_error().'<p><font color=red>'.$sql_a.'</font><p>';
}

$num_a = mysql_num_rows($result_a);


if ($num_a>1) {$hits='<p><strong>' . $num_a . '</strong> ' . 
def(
$orhits_d.' <strong id="terms">'.mkltgt($terms).'</strong>',
$orhits_e.' <strong id="terms">'.mkltgt($terms).'</strong>',
$orhits_f.' <strong id="terms">'.mkltgt($terms).'</strong>'
)
.'</p>';}

else { $hits='<p><strong>' . $num_a . '</strong> ' . 
def(
$orhit_d.' <strong id="terms">'.mkltgt($terms).'</strong>',
$orhit_e.' <strong id="terms">'.mkltgt($terms).'</strong>',
$orhit_f.' <strong id="terms">'.mkltgt($terms).'</strong>'
)
.'</p>';}

}

if ($num_a>0) ausgabe($hits,$result_a,$num_a,$terms,$alloc);
else {
	if(strpos('x'.$_GET['q'],'"')>0)die('<script>location.href=location.href.split("&q=")[0]+"&q='.str_replace('"','',$_GET['q']).'"</script>');	
	else leven($sfor);    
    }
}

//wenn null levenshtein erstellen und abfragen antwortstring abf�llen

function leven($sfor) {
global $nointranet;
global $doesfindpasswordprotectedcontent;
global $doesshowpreviewofsuchcontent;
global $leven;
global $turn;
global $term_d;
global $term_e;
global $term_f;
global $andhit_d;
global $andhit_e;
global $andhit_f;
global $andhits_d;
global $andhits_e;
global $andhits_f;
global $orhit_d;
global $orhit_e;
global $orhit_f;
global $orhits_d;
global $orhits_e;
global $orhits_f;
global $nohit_d;
global $nohit_e;
global $nohit_f;


$leven=def($nohit_d,$nohit_e,$nohit_f).'<br />';
for ($liii=0; $liii<tnuoc($sfor); $liii++) {

if ($doesfindpasswordprotectedcontent == 'yes') $sql_la="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c'";
else $sql_la="SELECT * FROM resources WHERE deleted!=1 $nointranet AND hidden=0 AND typ!='c' AND logins NOT LIKE '%:%'";

$result_la=mysql_query($sql_la);

if ($result_la==false) {
echo 'SQL_la-Query failed!<p>'.mysql_error().'<p><font color=red>'.$sql_la.'</font><p>';
}

$num_la = mysql_num_rows($result_la);

$all="";
for ($li_a=0; $li_a<$num_la; $li_a++) {
   $row_la = mysql_fetch_array($result_la);

$that=def('search_d','search_e','search_f');
$all.=($row_la[$that]);      
	     }
		 

$all=str_replace("\n"," ",$all);
$all=str_replace("\r"," ",$all);
$all=str_replace("\t"," ",$all);
$all=str_replace("("," ",$all);
$all=str_replace(")"," ",$all);
$all=str_replace("<"," ",$all);
$all=str_replace(">"," ",$all);
$all=str_replace(","," ",$all);
$all=str_replace("."," ",$all);
//$all=str_replace(";"," ",$all);
$all=str_replace(":"," ",$all);
$all=str_replace("!"," ",$all);
$all=str_replace("?"," ",$all);


$all=str_replace("'"," ",$all);
$all=str_replace("�"," ",$all);
$all=str_replace("["," ",$all);
$all=str_replace("]"," ",$all);
$all=str_replace("="," ",$all);
$all=str_replace("/"," ",$all);

//die(var_dump($all));



$all=explode(' ',$all);

$turn++;

//echo ','.$turn;

$input=$sfor[$liii];


///////////




// no shortest distance found, yet
$shortest = -1;

// loop through words to find the closest
foreach ($all as $word) {

   // calculate the distance between the input word,
   // and the current word
   $lev = levenshtein(strtolower($input), strtolower($word));

   // check for an exact match
   if ($lev == 0) {

       // closest word is this one (exact match)
       $closest = $word;
       $shortest = 0;

       // break out of the loop; we've found an exact match
       break;
   }

   // if this distance is less than the next found shortest
   // distance, OR if a next shortest word has not yet been found
   if ($lev <= $shortest || $shortest < 0) {
       // set the closest match, and shortest distance
       $closest  = $word;
       $shortest = $lev;
   }
}

$aclosest.=$closest.' ';

////////


}	 

suchen(trim(($aclosest)));

}

function ausgabe($hits,$result_a,$num_a,$terms,$alloc) {
global $resulttitlemaxlength;
global $doesfindpasswordprotectedcontent;
global $doesshowpreviewofsuchcontent;
global $leven;
global $getq;


  $keywords = preg_replace_callback(
    '/&#(.{5})/',
    function($match){
      return  "%u0".dechex(str_replace(";","",$match[1]));
	},
    $keywords
  );

if ($num_a>0) echo $leven.'';
echo $hits;

for ($i_a=0; $i_a<$num_a; $i_a++) {
$row_a = mysql_fetch_array($result_a);

if (str_replace(' ','',$row_a['title_e'])=='') $row_a['title_e']=$row_a['title_d'];
if (str_replace(' ','',$row_a['title_f'])=='') $row_a['title_f']=$row_a['title_d'];

$that=def('title_d','title_e','title_f');
$row_a[$that]=trim($row_a[$that]);
if (str_replace(' ','',$row_a[$that])=='') $row_a[$that]='*** missing metadata for this item ***';

$typ='';
include('getfile.inc.php');

$efile_d=explode('.',$file_d);
$efile_d=$efile_d[1];
$efile_e=explode('.',$file_e);
$efile_e=$efile_e[1];
$efile_f=explode('.',$file_f);
$efile_f=$efile_f[1];

$mustlogin='';
if (file_exists('r/'.$row_a['name'].'/.htaccess') || strpos($row_a['logins'],':')>1) $mustlogin='LOGIN! ';

if ($row_a['typ']=='p' && strpos($row_a['logins'],':')>1) $typ='<span style="font-size:60%">['.$mustlogin.']</span> ';
if ($row_a['typ']=='a') $typ='<span style="font-size:60%">['.$mustlogin.'jpgs]</span> ';
if ($row_a['typ']=='i') $typ='<span style="font-size:60%">['.$mustlogin.def($efile_d,$efile_e,$efile_f).']</span> ';
if ($row_a['typ']=='f') $typ='<span style="font-size:60%">['.$mustlogin.def($efile_d,$efile_e,$efile_f).']</span> ';
if ($row_a['typ']=='d') $typ='<span style="font-size:60%">['.$mustlogin.def($efile_d,$efile_e,$efile_f).']</span> ';
if ($row_a['typ']=='v') $typ='<span style="font-size:60%">['.$mustlogin.def($efile_d,$efile_e,$efile_f).']</span> ';
if ($row_a['typ']=='y') $typ='<span style="font-size:60%">[youtube]</span> ';
if ($row_a['typ']=='e') $typ='<span style="font-size:60%">[www]</span> ';
if ($row_a['typ']=='e' && str_replace('@','',$row_a[$that])!=$row_a[$that]) $typ='<span style="font-size:60%">[e-mail]</span> ';

$shellip='';

if ($row_a[$that]!=' . . .' && html_substr($row_a[$that],0,$resulttitlemaxlength)!=$row_a[$that]) $shellip=' . . .';

if ($row_a['typ']!='d') echo '<h5><a style="text-decoration:underline" onclick="if(document.getElementById(\'search_Input\'))this.href+=(\'&amp;q=\'+escape(document.getElementById(\'search_Input\').value.replace(/ /g,\',\').replace(/\+/g,\',\')))" href="?c='.$row_a['name'].'&amp;l='.$_GET['l'].'">'.$typ.html_substr($row_a[$that],0,$resulttitlemaxlength).$shellip.'</a></h5>';
else echo '<h5><a style="text-decoration:underline" href="r/'.$row_a['name'].'/'.def($file_d,$file_e,$file_f).'?t='.@filemtime('r/'.$row_a['name'].'/'.def($file_d,$file_e,$file_f)).'">'.$typ.html_substr($row_a[$that],0,$resulttitlemaxlength).$shellip.'</a></h5>';

$that=def('search_d','search_e','search_f');
if ($doesshowpreviewofsuchcontent == 'yes') echo '<p>'.alloc(($alloc),($row_a[$that])).'</p>';
else if (strpos($row_a['logins'],':')<1) echo '<p>'.alloc(($alloc),($row_a[$that])).'</p>';
else echo '<p>* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *&hellip;</p>';
}

}
//ausgabe mit alloc



?>
</p>
<script type="text/javascript">
function replace(string,text,by) {
// Replaces text with by in string
var strLength = string.length, txtLength = text.length;
if ((strLength == 0) || (txtLength == 0)) return string;
var i = string.indexOf(text);
if ((!i) && (text != string.substring(0,txtLength))) return string;
if (i == -1) return string;
var newstr = string.substring(0,i) + by;
if (i+txtLength < strLength)
newstr += replace(string.substring(i+txtLength,strLength),text,by);
return newstr;
}

document.fsearch2.q.focus();
function hglgtsrts2(){
if(document.getElementById('search_Input')){
document.getElementById('search_Input').value=document.getElementById('terms').innerHTML; 
document.getElementById('search_Input').value=replace(document.getElementById('search_Input').value,'&lt;','<');
document.getElementById('search_Input').value=replace(document.getElementById('search_Input').value,'&gt;','>');
document.getElementById('search_Input').value=replace(document.getElementById('search_Input').value,'&amp;','&');
}
}
jQuery(document).ready(function() {
hglgtsrts2();
});
</script>
<script src="sq.js"></script>