<?php if (!defined('PmWiki')) exit();

// This is a quick hack to enable notifications on (successfull)
// uploads. It was tested under pmwiki 2.1.11 (Aug 2006),
// with SecureAtachments and UploadVersioning enabled.
// Note: this solution may not work properly in future versions
// of pmwiki if the interfaces of the notify functions change.
//
// Installation: copy this file into your cookbook dir and add
// the common include_once('cookbook/notifyOnUploads.php'); to
// your local/config.php.
//
// Ver 0.1, Aug 2006, ThomasP


// Tap (smoothly!) into the HandleUpload control flow: first save 
// the original HandleUpload function ...
SDV($HandleActions['upload'], 'HandleUpload');
$NOUdefaultHandleUploadFunc = $HandleActions['upload'];
// ... then set my new function
$HandleActions['upload'] = 'HandleUploadWithNoties';

// Have a custom NotifyItem format:
SDV($NotifyUploadItemFmt,
  ' * {$FullName} . . . $PostTime by {$LastModifiedBy} (uploaded/updated $Upfilename)');

function HandleUploadWithNoties($pagename, $auth = 'upload') {
  global $IsPagePosted, $NotifyItemFmt, $NotifyUploadItemFmt, 
    $NOUdefaultHandleUploadFunc;
  
  // first call original handler:
  $NOUdefaultHandleUploadFunc($pagename, $auth);
  // now check if we were successful:
  if (@$_REQUEST['upresult'] == 'success') {
    // save original IsPagePosted value and NotifyItem format:
    $NOUisPagePostedSaved = $IsPagePosted;
    $NOUnotifyItemFmtSaved = $NotifyItemFmt;
    // set them to values such that everything goes through as we want
    // (triggers a new notify item in the format specifed above):
    $IsPagePosted = true;
    $NotifyItemFmt = $NotifyUploadItemFmt;
    // dont forget to replace some $variables in Fmt: (a kind of pre FmtPageName)
    $NotifyItemFmt = str_replace('$Upfilename', $_REQUEST['uprname'], $NotifyItemFmt);
    // shoot the bullet:
    NotifyUpdate($pagename, getcwd());
    // restore original isPagePost value:
    $IsPagePosted = $NOUisPagePostedSaved;
    $NotifyItemFmt = $NOUnotifyItemFmtSaved;
  }
}

