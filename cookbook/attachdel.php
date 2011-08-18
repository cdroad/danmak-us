<?php if (!defined('PmWiki')) exit();
/*  This script adds the ability to delete uploaded attachment files

    In order for it to work the (:attachlist:) markup in Site.UploadQuickReference
    can be changed to (:newattachlist:), which will add a delete option to
    all existing files.

    Author: Dan Weber (webmaster@drwhosting.net), with code contribution from the original upload.php script

    History
    -------
    0.02 - DW  - First usable version
    0.03 - DW  - Fixing a bug when no files are selected and "delete" is executed. Thank you Peter for reporting this
    0.04 - DW  - Version 0.03 broke more than it fixed. Now it should work fine.
    0.05 - jaf - Show upload input field after deleting an attachment (they were missing in older versions). Added by
                 jann.forrer@id.uzh.ch
    0.06 - CDA   pmwiki@christophedavid.org
               - Fixed bug for filenames containing simple and double quotes
               - suppression of bare PHP warning if unlink fails
               - 'Delete Checked Files' button not shown any longer if there is no file to check

    0.07 - CDA   pmwiki@christophedavid.org
               - Added variable $IsAttachDeleterecipeLoaded to allow

                 (:if enabled IsAttachDeleteRecipeLoaded:)
                 (:newattachlist:)
                 (:else:)
                 (:attachlist:)
                 (:ifend:)

                 in Site.UploadQuickReference
*/

define(ATTACH_DELETE_VERSION, '0.07');
$IsAttachDeleteRecipeLoaded = 1;


XLSDV('en',array(
  'ULdelsuccess' => 'successfully deleted',
  'ULdelfail' => 'failed to delete',
  'ULdelaction' => 'Delete Checked Files',
  'ULdelnofiles' => 'No files marked to delete'));

Markup('newattachlist', '<block',
  '/\\(:newattachlist\\s*(.*?):\\)/ei',
  "Keep('<ul>'.FmtNewUploadList('$pagename',PSS('$1')).'</ul>')");
SDVA($HandleActions, array('postdelattach' => 'HandleAttachmentDelete'));
SDVA($HandleAuth, array('postdelattach' => 'upload'));


function HandleAttachmentDelete($pagename, $auth = 'upload') {
  global $UploadDir, $UploadPrefixFmt, $PageStartFmt, $PageEndFmt;
  $page = RetrieveAuthPage($pagename, $auth, true, READPAGE_CURRENT);
  if (!$page) Abort("?cannot delete from $pagename");
  PCache($pagename,$page);
  $uploaddir = FmtPageName("$UploadDir$UploadPrefixFmt", $pagename);
  $out = array();
  $out[] = "<div id='wikiupload'>
            <h2 class='wikiaction'>$[Attachments for] \$FullName</h2>
            <h3>Delete Result</h3>
            <p>";
  if(count(@$_REQUEST['files']) == 0) {
    $out[] = "$[ULdelnofiles]<br>";
  }
  else {
    foreach(@$_REQUEST['files'] as $fn) {
      $fn = urldecode(preg_replace('/^[.\\/\\\\]*/', '', $fn));
      if (@unlink($uploaddir . "/" . $fn)) {
        $out[] = "$fn ... $[ULdelsuccess]<br>";
      }
      else {
        $out[] = "$fn ... $[ULdelfail]<br>";
      }
    }
  }
  $out[] = "<br></p></div>";
  /*
  SDV($PageDeleteFmt,array(FmtPageName($out, $pagename),
     $PageUploadFmt,array("
       <div id='wikiupload'>
       <h2 class='wikiaction'>$[Attachments for] {\$FullName}</h2>
       <form enctype='multipart/form-data' action='{\$PageUrl}' method='post'>
          <input type='hidden' name='n' value='{\$FullName}' />
          <input type='hidden' name='action' value='postupload' />
          <table border='0'>
            <tr><td align='right'>$[File to upload:]</td><td><input
             name='uploadfile' type='file' /></td></tr>
            <tr><td align='right'>$[Name attachment as:]</td>
            <td><input type='text' name='upname' value='' /><input
             type='submit' value=' $[Upload] ' /><br />
            </td></tr></table></form></div>"),
     "wiki:$[Site.UploadQuickReference]"));
  SDV($HandleDeleteFmt,array(&$PageStartFmt,&$PageDeleteFmt,&$PageEndFmt));
  PrintFmt($pagename,$HandleDeleteFmt);
  */
  HandleBrowse($pagename);
}



function FmtNewUploadList($pagename, $args) {
  global $UploadDir, $UploadPrefixFmt, $UploadUrlFmt, $EnableUploadOverwrite,
    $TimeFmt, $EnableDirectDownload, $HandleAuth;

  $opt = ParseArgs($args);
  if (@$opt[''][0]) $pagename = MakePageName($pagename, $opt[''][0]);
  if (@$opt['ext'])
    $matchext = '/\\.('
      . implode('|', preg_split('/\\W+/', $opt['ext'], -1, PREG_SPLIT_NO_EMPTY))
      . ')$/i';

  $uploaddir = FmtPageName("$UploadDir$UploadPrefixFmt", $pagename);
  $uploadurl = FmtPageName(IsEnabled($EnableDirectDownload, 1)
                          ? "$UploadUrlFmt$UploadPrefixFmt/"
                          : "\$PageUrl?action=download&amp;upname=",
                      $pagename);

  $dirp = @opendir($uploaddir);
  if (!$dirp) return '';
  $filelist = array();
  while (($file=readdir($dirp)) !== false) {
    if ($file{0} == '.') continue;
    if (@$matchext && !preg_match(@$matchext, $file)) continue;
    $filelist[$file] = $file;
  }
  closedir($dirp);
  $page = RetrieveAuthPage($pagename, $HandleAuth['postdelattach'], false, READPAGE_CURRENT);
  $out = array();
  if($page) {
    $out[] = FmtPageName("<form enctype='multipart/form-data' action='\$PageUrl' method='post'>", $pagename);
    $out[] = FmtPageName("<input type='hidden' name='n' value='\$FullName' />", $pagename);
    $out[] = "<input type='hidden' name='action' value='postdelattach' />";
  }

  asort($filelist);
  $overwrite = '';
  foreach($filelist as $file=>$x) {
    $name = PUE("$uploadurl$file");
    $stat = stat("$uploaddir/$file");
    if ($EnableUploadOverwrite)
      $overwrite = FmtPageName("<a class='createlink'
        href='\$PageUrl?action=upload&amp;upname=$file'>&nbsp;&Delta;</a>",
        $pagename);
    $delete = "";
    if($page) {
      $delete = FmtPageName("<input type='checkbox' name='files[]' value='" .   urlencode($file) . "' />", $pagename);
    }
    $out[] = "<li>$delete<a href='$name'>$file</a>$overwrite ... ".
      number_format($stat['size']) . " bytes ... " .
      strftime($TimeFmt, $stat['mtime']) . "</li>";
  }
  if($page) {
    if (count($filelist) > 0) {
       $out[] = FmtPageName("<br><input type='submit' value='$[ULdelaction]' />", $pagename);
       }
    $out[] = "</form>";
  }
  return implode("\n",$out);
}

