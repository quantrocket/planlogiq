<?php
/****************************************************************************
*                                                                           *
* Software : FRTF                                                           *
* Version  : 1.01 - Basic Edition                                           *
* Date     : 11/11/2004                                                     *
* Author   : Cristian Muraru for Smart Systems                              *
* License  : Freeware                                                       *
*                                                                           *
****************************************************************************/
include('param.inc.php');
require('rtfkeywords.inc.php');

class FRTF
{
//******************************************
//Syntax of a RTF file			   *
//<File>	'{' <header> <document> '}'*
//******************************************

//*************************************/*************************
//Header structure						*
//<header>	\rtf <charset> <deffont> \deff? 	        *
//			<fonttbl> <filetbl>? <colortbl>? 	*
//			<stylesheet>? <listtables>? 		*
//			<revtbl>? <rsidtable>? <generator>?	*
//***************************************************************


var $rtfVersion;		//rtf version
var $charSet;			//character set
//Unicode
var $unicodeRTF;		//unicode RTF
	var $uniCodePage;	//unicode code page
	var $uniCorresp;	//the number of bytes corresponding to a given \uN Unicode character
//End of Unicode

//*******************************************************************************************************************************************
//Font table group																															*
//<fonttbl>	'{' \fonttbl (<fontinfo> | ('{' <fontinfo> '}'))+ '}'																			*
//<fontinfo>	<fontnum> <fontfamily> <fcharset>? <fprq>? <panose>? <nontaggedname>? <fontemb>? <codepage>? <fontname> <fontaltname>? ';'  *
//*******************************************************************************************************************************************

var $defaultFonts='';		//default fonts
var $fontTable;			//font table

var $fntFamily;
var $fntCharset;
var $fntPitch;
var $fntPanose;
var $fntNontaggedname;
var $fntName;
var $fntAltName;
var $fntEmbType;
var $fntFontFileName;
var $fntCodePage;
//END of font table group

var $fileTable;			//file table
var $colorTable;		//color table
//Color table group
var $clrRed;			//red color
var $clrGreen;			//green color
var $clrBlue;			//blue color
//End of color table group
var $styleSheet;		//style sheet
var $stlSheetAutoIndex;         //indexul pentru style-urile generate automat
//Stylesheet group
var $stlFormatting;
var $stlDef;			//style definition
var $stlName;			//style sheet name
var $stlKeyCode;		//style sheet keucodes
var $stlAdditive;
var $stlBased;
var $stlNext;
var $stlAutoUpd;
var $stlHidden;
var $stlPers;
var $stlCompose;
var $stlReply;
var $stlID;
var $stlSemiHidden;
//End stylesheet group
var $listTable;			//list tables
var $pargrProp;			//paragraph group propierties
var $trackChanges;		//track changes (revision marks)
var $generator;			//generator

//***********************************************
//Document structure				*
//<document>	<info>? <docfmt>* <section>+	*
//***********************************************

//Information group
var $infTitle;			//Title of the document
var $infSubject;		//Subject of the document
var $infAuthor;			//Author of the document
var $infManager;		//*Manager of the author
var $infCompany;		//*Company of the author
var $infOperator;		//Person who last made changes to the document
var $infCategory;		//*Category of the document
var $infKeywords;		//Selected keywords for the document
var $infComment;		//Comments
var $infDoccomm;		//Comments displayed in the Summary Info or Properties dialog box in Word
var $infHlinkbase;		//*The base address that is used for the path of all relative hyperlinks inserted in the document
var $infCreatim;		//Creation time: \yrN	Year \moN	Month \dyN	Day \hrN	Hour \minN	Minute \secN	Seconds
var $infRevtim;			//Revision time: \yrN	Year \moN	Month \dyN	Day \hrN	Hour \minN	Minute \secN	Seconds
var $infPrintim;		//Last print time: \yrN	Year \moN	Month \dyN	Day \hrN	Hour \minN	Minute \secN	Seconds
var $infBuptim;			//Backup time: \yrN	Year \moN	Month \dyN	Day \hrN	Hour \minN	Minute \secN	Seconds
var $infEdmins;			//Total editing time (in minutes)
var $infVersion;		//Version number of the document
//TO BE IMPLEMENTED
var $infNofpages;		//Number of pages
var $infNofwords;		//Number of words
var $infNofchars;		//Number of characters including spaces
var $infNofcharsws;		//Number of characters not including spaces
var $infId;			//Internal ID number
//END TO BE IMPLEMENTED
//Userproperties group
var $usrGroup;
var $usrPropname;		//The name of the user-defined property
var $usrProptype;		//Specifies the type of the property:	3->Integer;5->Real number;7->Date;11->Boolean;30->Text
var $usrStaticval;		//The value of the property
var $usrLinkval;		//The name of a bookmark that contains the text to display as the value of the property.
//End of Information group

//Document formating properties
var $dfmtDefaultTab;	        //Default tab width in twips (the default is 720).
var $dfmtHyphHot;		//Hyphenation hot zone in twips (the amount of space at the right margin in which words are hyphenated).
var $dfmtHyphConsec;	        //The maximum number of consecutive lines that will be allowed to end in a hyphen. 0 means no limit.
var $dfmtHyphCaps;		//Toggles hyphenation of capitalized words (the default is on). Append 1 or leave control word by itself to toggle property on; append 0  to turn it off.
var $dfmtHyphAuto;		//Toggles automatic hyphenation (the default is off). Append 1 or leave control word by itself to toggle property on; append 0  to turn it off.
var $dfmtLineStart;		//Beginning line number (the default is 1).
var $dfmtFracWidth;		//Uses fractional character widths when printing (QuickDraw only).
var $dfmtNextFile;		//Destination. The argument is the name of the file to print or index next; it must be enclosed in braces. This is a destination control word.
var $dfmtTemplate;		//Destination. The argument is the name of a related template file; it must be enclosed in braces. This is a destination control word.
var $dfmtMakeBackup;	        //Backup copy is made automatically when the document is saved.
var $dfmtDefFormat;		//Tells the RTF reader that the document should be saved in RTF format.
var $dfmtPsOver;		//Prints PostScript over the text.
var $dfmtDocTemp;		//Document is a boilerplate document. For Word for Windows, this is a template; for Word for the Macintosh, this is a stationery file.
var $dfmtDefLang;		//Defines the default language used in the document used with a \plain control word.
var $dfmtDefLangfe;		//Default language ID for Asian versions of Word.
var $dfmtWindowCaption;	        //Sets the caption text for the document window. This is a string value.
var $dfmtDocType;		//An integer (0-2) that describes the document type for AutoFormat: 
								// 0->General Document (for formatting most documents, the default)
								// 1->Letter (for formatting letters, and used by Letter Wizard)
								// 2->E-mail (for formatting e-mail, and used by WordMail)		
var $dfmtDocSource;		//2 above
var $dfmtRenderingDoc;	        // 2 above
//TO BE IMPLEMENTED
var $dfmtJCompress;		//Compressing justification (default).
var $dfmtJExpand;		//Expanding justification.
var $dfmtLnonGrid;		//Define line based on the grid.
//END TO BE IMPLEMENTED

//Document views and zoom level
var $dfmtViewKind;		//An integer (0-5) that represents the view mode of the document.
							// 0->None
							// 1->Page Layout view
							// 2->Outline view
							// 3->Master Document view
							// 4->Normal view
							// 5->Online Layout view					
var $dfmtViewScale;		//Zoom level of the document; the N argument is a value representing a percentage (the default is 100).
var $dfmtViewZk;		//An integer (0 to 2) that represents the zoom kind of the document.
							// 0->None
							// 1->Full page
							// 2->Best fit
//Page Information 
var $dfmtPaperw;		//N Paper width in twips (the default is 12,240). 
var $dfmtPaperh;		//N Paper height in twips (the default is 15,840). 
var $dfmtPsz;			//N Used to differentiate between paper sizes with identical dimensions under Windows NT. 
							//Values 1–41 correspond to paper sizes defined in DRIVINI.H in the Windows 3.1 SDK (DMPAPER_ values). 
							//Values greater than or equal to 42 correspond to user-defined forms under Windows NT. 						
var $dfmtMargl;			//N Left margin in twips (the default is 1800). 
var $dfmtMargr;			//N Right margin in twips (the default is 1800). 
var $dfmtMargt;			//N Top margin in twips (the default is 1440). 
var $dfmtMargb;			//N Bottom margin in twips (the default is 1440). 

var $dfmtFacingp;		// Facing pages (activates odd/even headers and gutters). 
var $dfmtMargmirror;	        // Switches margin definitions on left and right pages. Used in conjunction with \facingp.
var $dfmtGutter;		//N Gutter width in twips (the default is 0). 

var $dfmtGutterPos;		//2 above
var $dfmtPgnStart;		//	Beginning page number (the default is 1).
var $dfmtTwoonone;		//	Print two logical pages on one physical page.
var $dfmtBookfold;		//	Book fold printing. Allows for printing documents that can easily be made into pamphlets. This will print two pages side by side in landscape mode, and will print to the back of the sheet if the printer supports duplex printing.
var $dfmtBookfoldrev;	        //	Reverse book fold printing for bidirectional languages.
var $dfmtBookfoldsheets;        //	Sheets per booklet; this should be a multiple of four.
var $dfmtDocFormat;		// portrait or landscape
var $dfmtWidowctrl;		// Enable widow and orphan control. 
//Page Borders 
var $dfmtPgbrdrhead;	        // Page border surrounds header.
var $dfmtPgbrdrfoot;	        // Page border surrounds footer.
var $dfmtPgbrdrt;		// Page border top. 
var $dfmtPgbrdrb;		// Page border bottom. 
var $dfmtPgbrdrl;		// Page border left. 
var $dfmtPgbrdrr;		// Page border right. 
var $dfmtBrdrart;		//N Page border art; the N argument is a value from 1-165 representing the number of the border. 
var $dfmtPgbrdropt;		//N    8->Page border measure from text. Always display in front option is set to off. 
							// 32->Page border measure from edge of page. Always display in front option is set to on.
							// 4032->Page border measure from edge of page. Always display in front option is set to off.
var $dfmtPgbrdrsnap;	// Align paragraph borders and table edges with page border. 
//The color, width, border style, and border spacing keywords for page borders are the same as the keywords defined for paragraph borders.
							
//Footnotes and Endnotes
var $dfmtNrFootEnd;
var $dfmtFet;			//N	Footnote/endnote type. This indicates what type of notes are present in the document.
						// 0	Footnotes only or nothing at all (the default)
						// 1	Endnotes only
						// 2	Both footnotes and endnotes
//For backward compatibility, if \fet1 is emitted, \endnotes or \enddoc will be emitted along with \aendnotes or \aenddoc. 
//RTF readers that understand \fet will need to ignore the footnote-positioning control words and use the endnote control words instead.
var $dfmtFtnSep;		//	Text argument separates footnotes from the document. This is a destination control word.
var $dfmtFtnSepc;		//	Text argument separates continued footnotes from the document. This is a destination control word.
var $dfmtFtNcn;			//	Text argument is a notice for continued footnotes. This is a destination control word.
var $dfmtAFtnSep;		//	Text argument separates endnotes from the document. This is a destination control word.
var $dfmtAFtnSepc;		//	Text argument separates continued endnotes from the document. This is a destination control word.
var $dfmtAFtNcn;		//	Text argument is a notice for continued endnotes. This is a destination control word.

var $dfmtFtnPlace;		// Two above
var $dfmtFtnJustif;		// 2 above
var $dfmtAFPlace;		// 2 above
var $dfmtAFJustif;		// 2 above
var $dfmtFtnStart;		//N	Beginning footnote number (the default is 1).
var $dfmtAFtnStart;		//N	Beginning endnote number (the default is 1).
var $dfmtFtnNumbPolicy;	        // 3 above
var $dfmtAFNumbPolicy;	        // 2 above
var $dfmtFtNumbStyle;	        //Al above
var $dfmtAFNumbStyle;	        //All above

//Linked Styles 
var $dfmtLinkstyles;	        // Update document styles automatically based on template.
//Compatibility Options 
var $dfmtCmptOptions; 
//Forms 
var $dfmtFormProt;
var $dfmtFormProp;
//Revision Marks 
var $dfmtRevProt;
var $dfmtRevProp;
//Comments (Annotations) 
var $dfmtAnnotprot;		// This document is protected for comments (annotations). The user cannot edit the document but can insert comments (annotations). 
//Bidirectional Controls 
var $dfmtDocDirection;          //2 above
//Drawing Grid
var $dfmtDgHSpace;		//N	Drawing grid horizontal spacing in twips (the default is 120).
var $dfmtDgVSpace;		//N	Drawing grid vertical spacing in twips (the default is 120).
var $dfmtDgHorigin;		//N	Drawing grid horizontal origin in twips (the default is 1701).
var $dfmtDgVorigin;		//N	Drawing grid vertical origin in twips (the default is 1984).
var $dfmtDgHShow;		//N	Show Nth horizontal gridline (the default is 3).
var $dfmtDgVShow;		//N	Show Nth vertical gridline (the default is 0).
var $dfmtDgSnap;		//	Snap to drawing grid.
var $dfmtDgMargin;		//	Drawing grid to follow margins.
//Click-and-Type
var $dfmtCTS;			//N	Index to the style to be used for Click-and-Type (0 is the default).
//Kinsoku Characters (Far East)
//TO BE IMPLEMENTED
var $dfmtJsksu;			//	Indicates that the strict Kinsoku set must be used for Japanese; \jsku should not be present if \ksulangN is present and the language N is Japanese. 
var $dfmtKsulang;		//N	N indicates which language the customized Kinsoku characters defined in the \fchars and \lchars destinations belong to.
var $dfmtFChars;		//\*\fchars	List of following Kinsoku characters.
var $dfmtLChars;		//\*\lchars	List of leading Kinsoku characters.
//END TO BE IMPLEMENTED


//***********************************************************
//Sections structure							            *
//<section>	 <secfmt>* <hdrftr>? <para>+ (\sect <section>)? *
//***********************************************************

var $NrSections=-1;
var $Sectionsfmt;

var $Sectionstxt;


//***************************************************************************************************************************
//Paragraph Text							            																	*
//<para>	<textpar> | <row>																								*
//<textpar>	<pn>? <brdrdef>? <parfmt>* <apoctl>* <tabdef>? <shading>? (/v /spv)? (\subdocument | <char>+) (\par <para>)?	*
//<row>	(<tbldef> <cell>+ <tbldef> \row) | (<tbldef> <cell>+ \row) | (<cell>+ <tbldef> \row)								*
//<cell>	(<nestrow>? <tbldef>?) & <textpar>+ \cell																		*
//<nestrow>	<nestcell>+ ‘{\*’\nesttableprops <tbldef> \nestrow ‘}’															*
//<nestcell>	<textpar>+ \nestcell																						*
//***************************************************************************************************************************


var $txtDocument;		//text to be writen in the rtf
var $buffer;                    //buffer holding in-memory the RTF file
var $LastBookmarkTag;
var $IsClosed;
var $Lng;	                //the language used for error messages
var $VerSupported;              // the last version of Microsoft Word supported by the generated RTF file

/****************************************************************************
*                                                                           *
*                              Public methods                               *
*                                                                           *
****************************************************************************/
function FRTF($Orientation='P',$PageFormat='A4',$Lang='EN')
{
	//initialisation of the object
	$this->Lng=$this->_getLang($Lang);
	$this->buffer='';
	$this->SetPaperSize($PageFormat);	
	$this->SetPageOrientation($Orientation);
}

function Write($Txt,$NewPar='Yes')
{
	global $RTFkeys;
	if(strtolower($NewPar)!='no')
	{
		$this->txtDocument.=$this->_leftKey($RTFkeys["Special Characters"]["key"][25]).SEPARATOR; //"\\par";
		$this->txtDocument.=$this->_leftKey($RTFkeys["Paragraph Formatting Properties"]["key"][46]);//"\\pard";
		$this->txtDocument.=$this->_leftKey($RTFkeys["Font (Character) Formatting Properties"]["key"][6]).SEPARATOR; //\\plain
	}
	$this->txtDocument.=SEPARATOR.$this->_escapeString($Txt);	
}

//Set the Windows caption
function SetWindowCaption($NewVal='')
{
	//Set the caption text for the document window. This is a string value.
	$this->_writeDocfmtProp2(" ".$NewVal,"dfmtWindowCaption",198,'');	
}
//Set dopcument format ->l for landscape format anything else for portrait format
function SetPageOrientation($NewVal='')
{
	if (strtolower(substr($NewVal,0,1))=='l')
	{		
		$this->_writeDocfmtProp1('',"dfmtDocFormat",139);
		$this->_changePapersize();
	}
}
//Set the view mode of the document
function SetDocumentView($Kind='',$Scale='',$Zoom='')
{
	$this->SetViewKind($Kind);
	$this->SetViewScale($Scale);
	$this->SetViewZk($Zoom);
}
function SetViewKind($NewVal='')
{
		//An integer (0-5) that represents the view mode of the document.
							// 0->None
							// 1->Page Layout view
							// 2->Outline view
							// 3->Master Document view
							// 4->Normal view
							// 5->Online Layout view
	if ($NewVal=='' or !isset($NewVal))
		{
			$NewVal='None';
		}
	switch(strtolower($NewVal))
		{
		case 'none':
			$NewVal=0;
			break;
		case 'page layout':case 'page':
			$NewVal=1;
			break;
		case 'outline':
			$NewVal=2;
			break;
		case 'master document':	case 'master':
			$NewVal=3;
			break;
		case 'normal':
			$NewVal=4;
			break;
		case 'online layout':case 'online':
			$NewVal=5;
			break;
		default:
			$NewVal=0;		
		}							
	$this->_writeDocfmtProp1(intval($NewVal),"dfmtViewKind",178);
}
//Set the zoom level of the document
function SetViewScale($NewVal='')
{
	//Zoom level of the document; the N argument is a value representing a percentage (the default is 100).						
	if ($NewVal=='' or !isset($NewVal) or intval($NewVal)<0 or intval($NewVal)>100)
		{
			$NewVal=100;
		}	
	$this->_writeDocfmtProp1(intval($NewVal),"dfmtViewScale",16);
		
}
//Set the zoom kind of the document
function SetViewZk($NewVal='')
{
		//An integer (0 to 2) that represents the zoom kind of the document.
							// 0->None
							// 1->Full page
							// 2->Best fit
	if (($NewVal=='') or !isset($NewVal))
		{
			$NewVal='None';
		}
	switch(strtolower($NewVal))
		{
		case 'none': 
			$NewVal=0;
			break;		
		case 'full page':case 'full':case 'page':
			$NewVal=1;
			break;
		case 'best fit':case 'best':case 'fit':
			$NewVal=2;
			break;
		default:
			$NewVal=0;		
		}							
	$this->_writeDocfmtProp1(intval($NewVal),"dfmtViewZk",196);														
}	

//Set Paper size
function SetPaperSize($NewVal='')
{
	global $inch,$cm;
	//$inch=1440;
	$mm=$cm*10;	
/*
	  1       Letter (8.5 x 11 in.)
      5       Legal (8.5 x 14 in.)
      7       Executive (7.25 x 10.5 in.)
      8       A3 (297 x 420 mm)
      9       A4 (210 x 297 mm)
*/
	switch(strtolower($NewVal))
	{
		case 'letter': //8 1/2 x 11 in
			$Pagew=8.5*$inch;$Pageh=11*$inch;$PaperSize=1;break;		
		case 'legal': //8 1/2 x 14 in 
			$Pagew=8.5*$inch;$Pageh=14*$inch;$PaperSize=5;break;
		case 'executive': // 7 1/4 x 10 1/2 in  
			$Pagew=7.25*$inch;$Pageh=10.5*$inch;$PaperSize=7;break;
		case 'a3': // 297 x 420 mm	
			$Pagew=297*$mm;$Pageh=420*$mm;$PaperSize=8;break;		
		case 'a4': // 210 x 297 mm
			$Pagew=210*$mm;$Pageh=297*$mm;$PaperSize=9;break;				
		default:
			//letter
			$Pagew=8.5*$inch;
			$Pageh=11*$inch;
	}
	if(isset($this->dfmtDocFormat))
	{
		$this->_writeDocfmtProp1(intval($Pageh),"dfmtPaperw",37);				
		$this->_writeDocfmtProp1(intval($Pagew),"dfmtPaperh",38);														
	}
	else
	{
		$this->_writeDocfmtProp1(intval($Pagew),"dfmtPaperw",37);				
		$this->_writeDocfmtProp1(intval($Pageh),"dfmtPaperh",38);													
	}
	$this->_writeDocfmtProp1($PaperSize,"dfmtPsz",26);														
}

function Open()
{
	global $RTFkeys;
	//Begin RTF document
	$this->_begindoc();
	$this->_writeRTFVersion();		
	$this->_writeCharSet("");			
}

function Close()
{
	//Terminate document
	$this->_writeHeader();
	$this->_writeInfo();
	$this->_writeDocFmt();
	$this->buffer.=$this->txtDocument;
	$this->_enddoc();
	$this->IsClosed=true;
	
}
function Output($File='')
{
	//Output RTF to file or browser
	global $HTTP_ENV_VARS;

	if (!($this->IsClosed))
	{
		$this->Close();
	}
	if($File=='')
	{
		//Send to browser
		Header('Content-Type: application/rtf');
		if(headers_sent())
			$this->Error(1);
		Header('Content-Length: '.strlen($this->buffer));
		Header('Content-disposition: inline; filename=doc.rtf');
		echo $this->buffer;				
	}
	else
	{
		//Save file locally
		$f=fopen($File,'wb');
		if(!$f)
			$this->Error(2);
		fwrite($f,$this->buffer,strlen($this->buffer));
		fclose($f);
	}
	
}
function LastWordSupported()
{
	return WORDVer.$this->VerSupported;
}
/****************************************************************************
*                                                                           *
*                              Private methods                              *
*                                                                           *
****************************************************************************/
function Error($ErrCode)
{
	die("<b>FRTF (V ".FRTF_VERSION.") ".$this->_getError(0)." : </b> ".$this->_getError($ErrCode));
}

//Write unicode
function _writeUnicode()
{
	global $RTFkeys;
	$this->unicodeRTF=$this->_leftKey($RTFkeys["Unicode RTF"]["key"][1]).$this->uniCodePage;
}

function _writeHeader()
{
	$this->_out($this->rtfVersion);
	$this->_out($this->charSet);
	$this->_writeUnicode();	
	$this->_out($this->unicodeRTF);
	
	$this->_out($this->defaultFonts);			
	
	$this->_out($this->dfmtDefLang);
	$this->_out($this->dfmtDefLangfe);	
	
	//$this->_writeFontTable();	
	$this->_out($this->fontTable);
	$this->_out($this->fileTable);
	
	//$this->_writeColorTable();	
	$this->_out($this->colorTable);
	
	//$this->_writeStyleSheetTable();	
	$this->_out($this->styleSheet);	
	
	$this->_out($this->listTable);
	$this->_out($this->pargrProp);
	$this->_out($this->trackChanges);
	$this->_out($this->generator);
	
}
function _writeInfo()
{
	global $RTFkeys;
	$this->_out(LEFT_BRACE);
	$this->_out($this->_leftKey($RTFkeys["Information Group"]["key"][19]));
	$this->_out($this->infTitle);
	$this->_out($this->infSubject);
	$this->_out($this->infAuthor);
	$this->_out($this->infManager);
	$this->_out($this->infCompany);
	$this->_out($this->infOperator);
	$this->_out($this->infCategory);
	$this->_out($this->infKeywords);
	$this->_out($this->infComment);
	$this->_out($this->infDoccomm);
	$this->_out($this->infHlinkbase);
	$this->_out($this->infCreatim);
	$this->_out($this->infRevtim);
	$this->_out($this->infPrintim);
	$this->_out($this->infBuptim);
	$this->_out($this->infEdmins);
	$this->_out($this->infVersion);
	$this->_out($this->infNofpages);
	$this->_out($this->infNofwords);
	$this->_out($this->infNofchars);
	$this->_out($this->infNofcharsws);
	$this->_out($this->infId);
	$this->_out($this->usrGroup);
	$this->_out(RIGHT_BRACE);
}

function _writeGenerator()
{
	global $RTFkeys;
	global $txtGenerator;
	$this->generator=LEFT_BRACE.OPTIONAL;
	$this->generator.=$this->_leftKey($RTFkeys["Generator"]["key"][0]);
	$this->generator.=SEPARATOR;
	$this->generator.=$txtGenerator;
	$this->generator.=RIGHT_BRACE;
}
function _writeRTFVersion($NewVersion="1")
{
	global $RTFkeys;
	$this->rtfVersion=$this->_leftKey($RTFkeys["RTF Version"]["key"][0]).$NewVersion;	
}

function _writeCharSet($NewCharSet="ANSI")
{
	global $RTFkeys;
	switch (strtolower($NewCharSet)){
		case "ansi";
			$this->charSet.=$this->_leftKey($RTFkeys["Character Set"]["key"][0]);
			break;
		case "mpple macintosh":
			$this->charSet.=$this->_leftKey($RTFkeys["Character Set"]["key"][3]);
			break;			
		case "bm pc code page 437":
			$this->charSet.=$this->_leftKey($RTFkeys["Character Set"]["key"][2]);		
			break;
		case "ibm pc code page 850":
			$this->charSet.=$this->_leftKey($RTFkeys["Character Set"]["key"][3]);		
			break;
		default:
			$this->charSet.=$this->_leftKey($RTFkeys["Character Set"]["key"][0]);
	}
}

function _writeCreationTime($dtNewTime='')
{	
	global $RTFkeys;
	if($dtNewTime=='' or !isset($dtNewTime))
	{
			$an=date("Y");
			$luna=date("m");
			$zi=date("d");	
			$hora=date("H");
			$minut=date("i");
			$secunda=date("s");			
	}
	else
	{
		if (checkdate(date("m",strtotime($dtNewTime)),date("d",strtotime($dtNewTime)),date("Y",strtotime($dtNewTime))))
		{
			$an=date("Y",strtotime($dtNewTime));
			$luna=date("m",strtotime($dtNewTime));
			$zi=date("d",strtotime($dtNewTime));
			$hora=date("H",strtotime($dtNewTime));
			$minut=date("i",strtotime($dtNewTime));
			$secunda=date("s",strtotime($dtNewTime));
		}
		else
		{
			$an=date("Y");
			$luna=date("m");
			$zi=date("d");	
			$hora=date("H");
			$minut=date("i");
			$secunda=date("s");		
		}
	}
	$this->infCreatim=LEFT_BRACE;	
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][21]);
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][34]);
	$this->infCreatim.=$an;
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][9]);
	$this->infCreatim.=$luna;
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][16]);
	$this->infCreatim.=$zi;	
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][4]);
	$this->infCreatim.=$hora;
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][12]);	
	$this->infCreatim.=$minut;	
	$this->infCreatim.=$this->_leftKey($RTFkeys["Information Group"]["key"][10]);	
	$this->infCreatim.=$secunda;		
	$this->infCreatim.=RIGHT_BRACE;	
}

function _changePapersize()
{
	global $RTFkeys,$inch;
	$temp=$this->dfmtPaperw;
	$temp=substr($temp,strlen($this->_leftKey($RTFkeys["Document Formatting Properties"]["key"][37])));
	$temp1=$this->dfmtPaperh;
	$temp1=substr($temp1,strlen($this->_leftKey($RTFkeys["Document Formatting Properties"]["key"][38])));	
	$this->dfmtPaperw='';
	$this->dfmtPaperh='';
	if(intval($temp1)==0) $temp1=11*$inch;
	if(intval($temp)==0) $temp=8.5*$inch;	
	$this->_writeDocfmtProp1($temp1,"dfmtPaperw",37);
	$this->_writeDocfmtProp1($temp,"dfmtPaperh",38);
}

function _UpdateVerSuported($NewVer='')
{
	if (!($NewVer=='' or !isset($NewVer)))
	{
		if($this->VerSupported=='') 
			$this->VerSupported=$NewVer;
		else
			if(intval($NewVer)>$this->VerSupported)
					$this->VerSupported=$NewVer;
	}
}
function _leftKey($stKey)
{
	$pos = strrpos($stKey, " ");
	if (is_int($pos)){
		$this->_UpdateVerSuported(substr($stKey,$pos,strlen($stKey)));
		$retval=substr($stKey, 0, $pos); 
	}
	else{
		$retval=$stKey;
	}
	return $retval;
}

function _escapeString($sir)
{
	$Newsir="";
	for($i=0;$i<strlen($sir);$i++)
	{
		$Newsir.=$this->_escapeChar($sir[$i]);
	}
	return $Newsir;
}

function _escapeChar($chr)
{	
	$escChar=ESCAPE;
	$retval="";	
	if (ord($chr)>=0x00 and ord($chr)<0x20)
		{
		$retval=$escChar."'".base_convert(ord($chr),10,16);
		}
	if (ord($chr)>=0x20 and ord($chr)<0x80)
		{$retval=$chr;}
	if (ord($chr)>=0x80 and ord($chr)<0xFF)
		{
		$retval=$escChar."'".base_convert(ord($chr),10,16);
		}		
	switch(ord($chr)){
		case 0x5C:
		case 0x7B:
		case 0x7D:
		$retval=$escChar."'".base_convert(ord($chr),10,16);
	}
	return $retval;
}


function _begindoc()
{
	$this->_doDefaults();
	$this->_out(LEFT_BRACE);
}
function _enddoc()
{
	$this->_out(RIGHT_BRACE);
}
function _out($sir)
{
	//Add the text to the document
	$this->buffer.=$sir;
}	

//Write a document formating property -type 1
function _getIndexInArray($Loc='')
{
	switch(strtolower($Loc))
	{
		case 'sec':
			$Index='Section Formatting Properties';
			break;
		case 'doc':
			$Index='Document Formatting Properties';
			break;
		case 'spec':
			$Index="Special Characters";
			break;			
		default:		
			$Index='Document Formatting Properties';		
	}
	return $Index;
}
function _getProperty($Loc,$Prop)
{
	switch(strtolower($Loc))
	{
		case 'sec':
			$P='Sectionsfmt[$this->NrSections][\''.$Prop.'\']';
			break;
		case 'doc':
			$P=$Prop;
			break;
		case 'spec':
			$P=$Prop;
			break;
		default:		
			$P=$Prop;	
	}
	return $P;
}
function _writeProp1($Loc,$NewVal,$Prop,$idx,$Plus='')
{
	//nefolosita
	global $RTFkeys;
	
	$Index=$this->_getIndexInArray($Loc);
	$Prop=$this->_getProperty($Loc,$Prop);
	if ($Plus=='')
	{
		$this->$Prop=$this->_leftKey($RTFkeys[$Index]["key"][$idx]);	
	}
	else
	{
		$this->$Prop.=$this->_leftKey($RTFkeys[$Index]["key"][$idx]);		
	}
	$this->$Prop.=$NewVal;
}
function _writeDocfmtProp1($NewVal,$Prop,$idx,$Plus='')
{
	$this->_writeProp1('Doc',$NewVal,$Prop,$idx,$Plus);
}

//UNwrite a document formating property
function _UNwriteDocfmtProp1($NewVal,$Prop,$idx)
{
	$this->$Prop='';
}
//Write a document formating propertie -type 2
function _writeDocfmtProp2($NewVal,$Prop,$idx,$opt)
{
	$this->_writeProp2('Doc',$NewVal,$Prop,$idx,$opt);
}
function _writeProp2($Loc,$NewVal,$Prop,$idx,$opt)
{
	global $RTFkeys;
	
	$Index=$this->_getIndexInArray($Loc);
	$this->$Prop=LEFT_BRACE;
	if (isset($opt))
		$this->$Prop.=OPTIONAL;
	$this->$Prop.=$this->_leftKey($RTFkeys[$Index]["key"][$idx]);	
	$this->$Prop.=SEPARATOR;		
	$this->$Prop.=$NewVal;
	$this->$Prop.=RIGHT_BRACE;			
}
function _writeDocFmt()
{
	$this->_out($this->dfmtWidowctrl);	
	$this->_out($this->dfmtDefaultTab);
	$this->_out($this->dfmtHyphHot);
	$this->_out($this->dfmtHyphConsec);
	$this->_out($this->dfmtHyphCaps);
	$this->_out($this->dfmtHyphAuto);
	$this->_out($this->dfmtLineStart);
	$this->_out($this->dfmtFracWidth);
	$this->_out($this->dfmtNextFile);
	$this->_out($this->dfmtTemplate);
	$this->_out($this->dfmtWindowCaption);
	$this->_out($this->dfmtDocType);
	//$this->_out($this->dfmtFromText);
	//$this->_out($this->dfmtFromHtml);
	$this->_out($this->dfmtDocSource);			
	$this->_out($this->dfmtViewKind);
	$this->_out($this->dfmtViewScale);
	$this->_out($this->dfmtViewZk);
	$this->_out($this->dfmtPaperw);
	$this->_out($this->dfmtPaperh);
	$this->_out($this->dfmtPsz);	
	$this->_out($this->dfmtMargl);		
	$this->_out($this->dfmtMargr);		
	$this->_out($this->dfmtMargt);			
	$this->_out($this->dfmtMargb);		
	$this->_out($this->dfmtFacingp);
	$this->_out($this->dfmtMargmirror);			
	$this->_out($this->dfmtTwoonone);				
	$this->_out($this->dfmtGutter);
	$this->_out($this->dfmtGutterPos);		
	//$this->_out($this->dfmtHorzDoc);
	//$this->_out($this->dfmtVertDoc);
	$this->_out($this->dfmtRenderingDoc);
	$this->_out($this->dfmtDocFormat);	
	$this->_out($this->dfmtPgnStart);		
	$this->_out($this->dfmtBookfold);		
	$this->_out($this->dfmtBookfoldrev);		
	$this->_out($this->dfmtBookfoldsheets);
	$this->_out($this->dfmtPgbrdrhead);
	$this->_out($this->dfmtPgbrdrfoot);			
	$this->_out($this->dfmtPgbrdrt);				
	$this->_out($this->dfmtPgbrdrb);				
	$this->_out($this->dfmtPgbrdrl);				
	$this->_out($this->dfmtPgbrdrr);				
	$this->_out($this->dfmtBrdrart);				
	$this->_out($this->dfmtPgbrdropt);				
	$this->_out($this->dfmtPgbrdrsnap);
	$this->_out($this->dfmtCmptOptions);
	$this->_out($this->dfmtFormProt);	
	$this->_out($this->dfmtFormProp);
	$this->_out($this->dfmtRevProt);	
	$this->_out($this->dfmtRevProp);	
	$this->_out($this->dfmtAnnotprot);	
	$this->_out($this->dfmtDocDirection);
	$this->_out($this->dfmtDgHSpace);
	$this->_out($this->dfmtDgVSpace);
	$this->_out($this->dfmtDgHorigin);
	$this->_out($this->dfmtDgVorigin);
	$this->_out($this->dfmtDgHShow);
	$this->_out($this->dfmtDgVShow);
	$this->_out($this->dfmtDgSnap);
	$this->_out($this->dfmtDgMargin);
	$this->_out($this->dfmtCTS);
	//**** foot and end notes
	$this->_out($this->dfmtFet);
	//footnote definition
	$this->_out($this->dfmtFtnPlace);
	$this->_out($this->dfmtFtnJustif);
	$this->_out($this->dfmtFtnStart);
	$this->_out($this->dfmtFtnNumbPolicy);
	$this->_out($this->dfmtFtNumbStyle);		
	//endnote definition
	$this->_out($this->dfmtAFPlace);
	$this->_out($this->dfmtAFJustif);
	$this->_out($this->dfmtAFtnStart);
	$this->_out($this->dfmtAFNumbPolicy);
	$this->_out($this->dfmtAFNumbStyle);	
}

function _doDefaults()
{
	$this->_writeCreationTime('');
	//$this->_writeGenerator();
}

function _getError($Err_code='') 
{
	$Err_message = array();
	$Err_code    = (int) $Err_code;
	
	switch ($this->Lng) 
	{
		// Romanian (ro)
		case 'ro':
			$Err_message[0] = "mesaj de eroare";
			$Err_message[1] = "Niste date au fost deja trimise catre browser, nu pot trimite fisierul RTF!";
			$Err_message[2] = "Nu pot crea fisierul de iesire !";
		break;
	
		// French (fr)
		case 'fr':
			$Err_message[0] = "message d\'erreure";		
			$Err_message[1] = "Quelques donneés ont deja été envoyés vers le browser, on peut pas envoyé le fichier RTF!";
			$Err_message[2] = "On peut pas creér le fichier !";
		break;		

		// Italian (it)
		case 'it':
			$Err_message[0] = "messagio de eroare";		
			$Err_message[1] = "Il file RTF non e\' stato salvato, ";
			$Err_message[2] = "Unable to create output file !";
		break;
		
		// German (de)
		case 'de':
			$Err_message[0] = "mesaj de eroare";		
			$Err_message[1] = "Some data has already been output to browser, can\'t send RTF file!";
			$Err_message[2] = "Unable to create output file !";		
		break;
		
		//English
		default:
			$Err_message[0] = "error message";		
			$Err_message[1] = "Some data has already been output to browser, can\'t send RTF file!";
			$Err_message[2] = "Unable to create output file !";			
	}
		
	return $Err_message[$Err_code];	
}

function _getLang($Lang='')
{
	switch(strtolower($Lang))
	{
		case 'english':	case 'engleza':	case 'en':	case 'e':
			$lng='en';break;
		case 'romanian':	case 'romana':	case 'ro':	case 'r':
			$lng='ro';break;		
		case 'french':	case 'franceza':	case 'fr':	case 'f':
			$lng='fr';break;		
		case 'italian':	case 'italiana':	case 'it':	case 'i':
			$lng='it';break;		
		case 'german':	case 'germana':		case 'de':	case 'd':
			$lng='de';break;		
		default:  			$lng='en';break;
	}
	return $lng;
}
//End of class	
}
?>
