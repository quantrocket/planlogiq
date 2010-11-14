<?php
/*
********************************************
* the keywords of the RTF                  *
* (Section => (keyword() type() meaning()))*
********************************************
*/

$RTFkeys=array(
"Associated Character Properties"=>array(
"key"=>array("\\aul","\\ashad","\\ascaps","\\aoutl","\\auldb","\\alang","\\ai","\\aup","\\aulw","\\astrike","\\aulnone","\\dbch 7.0",
"\\afs","\\af","\\aexpnd","\\loch 7.0","\\auld","\\adn","\\acf","\\acaps","\\ab","\\hich 7.0"),
"type"=>array("Toggle","Toggle","Toggle","Toggle","Toggle","Value","Toggle","Value","Toggle","Toggle","Toggle","Flag","Value",
"Value","Value","Flag","Toggle","Value","Value","Toggle","Toggle","Flag"),
"meaning"=>array()),

"Bookmarks"=>array(
"key"=>array(0=>"\\bkmkcoll",1=>"\\bkmkcolf",2=>"\\bkmkstart",3=>"\\bkmkend"),
"type"=>array("Value","Value","Destination","Destination"),
"meaning"=>array()),

"Bullets and Numbering"=>array(
"key"=>array(0=>"\\pnql",1=>"\\pncf",2=>"\\pnseclvl",3=>"\\pnscaps",4=>"\\pncard",5=>"\\pncaps",6=>"\\pnqc",7=>"\\pnqr",8=>"\\pndec",
9=>"\\pnrestart",10=>"\\pnchosung 97",11=>"\\pnbidib 2000",12=>"\\pnprev",13=>"\\pngbnumk 97",14=>"\\pnindent",15=>"\\pniroha 7.0",
16=>"\\pnirohad 7.0",17=>"\\pnlcltr",18=>"\\pnlcrm",19=>"\\pnlvl",20=>"\\pnlvlblt",21=>"\\pnlvlbody",22=>"\\pnlvlcont",23=>"\\pnnumonce",
24=>"\\pnord",25=>"\\pndbnuml 97",26=>"\\pngbnuml 97",27=>"\\pncnum 7.0",28=>"\\pngbnumd 97",29=>"\\pngbnum 97",30=>"\\pnganada 97",
31=>"\\pnganada 97",32=>"\\pnfs",33=>"\\pnf",34=>"\\pndecd 7.0",35=>"\\pnacross",36=>"\\pndbnumt 97",37=>"\\pndbnumk 97",38=>"\\pndbnumd 97",
39=>"\\pndbnum 7.0",40=>"\\pnhang",41=>"\\pnzodiacl 97",42=>"\\pnuldashdd 7.0",43=>"\\pnbidia 2000",44=>"\\pnulhair 7.0",45=>"\\pnaiud 7.0",
46=>"\\pnulth 7.0",47=>"\\pnulw",48=>"\\pnulwave 7.0",49=>"\\pnzodiac 97",50=>"\\pnuldashd 7.0",51=>"\\pnzodiacd 97",52=>"\\pnuldb",
53=>"\\pgngbnuml 97",54=>"\\pgngbnumk 97",55=>"\\pgngbnumd 97",56=>"\\pgngbnum 97",57=>"\\pgnganada 97",58=>"\\pgndbnumt 97",
59=>"\\pgndbnumk 97",60=>"\\pgncnum 97",61=>"\\pgnchosung 97",62=>"\\pgnzodiac 97",63=>"\\pnsp",64=>"\\pnb",65=>"\\pnaiueod 97",
66=>"\\pnaiueo 97",67=>"\\pni",68=>"\\pnaiu 7.0",69=>"\\pnordt",70=>"\\pnulnone",71=>"\\pgnzodiacl 97",72=>"\\pnuldash 7.0",
73=>"\\pnstart",74=>"\\pnucltr",75=>"\\pnuld",76=>"\\pn",77=>"\\pnucrm",78=>"\\pntxtb",79=>"\\pntxta",80=>"\\pntext",81=>"\\pgnzodiacd 97",
82=>"\\pnstrike",83=>"\\pnul"),
"type"=>array(),
"meaning"=>array()),

"Character Set"=>array(
"key"=>array(0=>"\\ansi",1=>"\\pca",2=>"\\pc",3=>"\\mac",4=>"\\embo 97",5=>"\\striked1 97",6=>"\\impr 97",7=>"\\chbgdkfdiag 97",
8=>"\\revauthdelN 97",9=>"\\crdateN 97",10=>"\\revdttmdelN 97",11=>"\\charscalex 97",12=>"\\chbgbdiag 97",13=>"\\chbgcross 97",
14=>"\\chbgdcross 97",15=>"\\chbgdkbdiag 97",16=>"\\chbgdkdcross 97",17=>"\\ulth 97",18=>"\\chbgdkhoriz 97",19=>"\\chshdngN 97",
20=>"\\chbgfdiag 97",21=>"\\chbghoriz 97",22=>"\\chbgvert 97",23=>"\\crauthN 97",24=>"\\chbrdr 97",25=>"\\chcbpat 97",26=>"\\chcfpat 97",
27=>"\\chbgdkvert 97",28=>"\\chbgdkcross 97"),
"type"=>array("Flag","Flag","Flag","Flag","Toggle","Toggle","Toggle","Flag","Value","Value","Value","Value","Flag","Flag","Flag","Flag",
"Flag","Toggle","Flag","Value","Flag","Flag","Flag","Value","Flag","Value","Value","Flag","Flag"),
"meaning"=>array()),

"Code Page Support"=>array(
"key"=>array(0=>"\\cpg"),
"type"=>array("Value"),
"meaning"=>array()),

"Color Table"=>array(
"key"=>array(0=>"\\red",1=>"\\green",2=>"\\colortbl",3=>"\\blue"),
"type"=>array("Value","Value","Destination","Value"),
"meaning"=>array()),

"Comments (Annotations)"=>array(
"key"=>array(0=>"\\annotation",1=>"\\atndate",2=>"\\atnauthor 2002",3=>"\\atntime",4=>"\\atnid",5=>"\\atnparent 2002",6=>"\\atnref",
7=>"\\atnicn",8=>"\\atrfstart",9=>"\\atrfend"),
"type"=>array("Destination","Destination","Destination","Destination","Destination","Destination","Destination","Destination",
"Destination","Destination"),
"meaning"=>array()),

"Control Words Introduced by Other Microsoft Products"=>array(
"key"=>array("\\mhtmltag","\\urtfN","\\pwdN","\\htmlrtf","\\htmltag","\\htmlbase"),
"type"=>array("Destination","Destination","Destination","Toggle","Destination","Flag"),
"meaning"=>array()),

"Default Fonts"=>array(
"key"=>array("\\stshfbiN 2002","\\stshfdbchN 2002","\\stshfhichN 2002","\\stshflochN 2002"),
"type"=>array("Value","Value","Value","Value"),
"meaning"=>array()),

"Document Formatting Properties"=>array(
"key"=>array(0=>"\\noextrasprl",1=>"\\revprop",2=>"\\oldlinewrap 97",3=>"\\oldas 2000",4=>"\\noxlattoyen 97",5=>"\\noultrlspc 97",
6=>"\\notabind",7=>"\\nospaceforul 97",8=>"\\margr",9=>"\\nolead 97",10=>"\\nocompatoptions 2002",11=>"\\revprot",12=>"\\nocolbal",
13=>"\\nobrkwrptbl 2002",14=>"\\nextfile",15=>"\\msmcap 97",16=>"\\viewscale 97",17=>"\\rtldoc",18=>"\\margt",19=>"\\nolnhtadjtbl 2000",
20=>"\\pgbrdropt 97",21=>"\\pgnstart",22=>"\\prcolbl",23=>"\\printdata",24=>"\\private 97",25=>"\\psover",26=>"\\psz",
27=>"\\rempersonalinfo 2002",28=>"\\margmirror",29=>"\\pgbrdrsnap 97",30=>"\\pgbrdrr 97",31=>"\\revisions",32=>"\\pgbrdrl 97",
33=>"\\pgbrdrhead 97",34=>"\\pgbrdrfoot 97",35=>"\\pgbrdrb 97",36=>"\\revbar",37=>"\\paperw",38=>"\\paperh",39=>"\\otblrul",
40=>"\\pgbrdrt 97",41=>"\\bookfoldrev 2002",42=>"\\alntblind 2000",43=>"\\deflang",44=>"\\defformat",45=>"\\cvmme",46=>"\\cts 2000",
47=>"\\brkfrm",48=>"\\deftab",49=>"\\bookfoldsheets 2002",50=>"\\dghorigin 7.0",51=>"\\bookfold 2002",52=>"\\bdrrlswsix 2000",
53=>"\\bdbfhdr 97",54=>"\\asianbrkrule 2002",55=>"\\ApplyBrkRules 2002",56=>"\\gutter",57=>"\\brdrart 97",58=>"\\dgvspace 7.0",
59=>"\\enddoc",60=>"\\donotshowprops 2002",61=>"\\donotshowmarkup 2002",62=>"\\donotshowinsdel 2002",63=>"\\donotshowcomments 2002",
64=>"\\doctype 97",65=>"\\deflangfe 97",66=>"\\dntblnsbdb 97",67=>"\\allprot",68=>"\\dgvshow 7.0",69=>"\\dgvorigin 7.0",
70=>"\\dgsnap 7.0",71=>"\\dgmargin 97",72=>"\\dghspace 7.0",73=>"\\dghshow 7.0",74=>"\\doctemp",75=>"\\aftnnauc",76=>"\\annotprot",
77=>"\\aftnndbnumk 97",78=>"\\aftnndbnumd 97",79=>"\\aftnndbnum 97",80=>"\\aftnndbar 97",81=>"\\aftnncnum 97",82=>"\\aftnnganada 97",
83=>"\\aftnnchi",84=>"\\aftnngbnum 97",85=>"\\aftnnar",86=>"\\aftnnalc",87=>"\\aftncn",88=>"\\aftnbj",89=>"\\aendnotes",
90=>"\\aenddoc",91=>"\\aftnnchosung 97",92=>"\\aftnnzodiacd 97",93=>"\\allowfieldendsel 2002",94=>"\\aftntj",95=>"\\aftnstart",
96=>"\\aftnsepc",97=>"\\aftnsep",98=>"\\aftnrstcont",99=>"\\aftnndbnumt 97",100=>"\\aftnnzodiacl 97",101=>"\\facingp",
102=>"\\aftnnzodiac 97",103=>"\\aftnnruc",104=>"\\aftnnrlc",105=>"\\aftnngbnuml 97",106=>"\\aftnngbnumk 97",107=>"\\aftnngbnumd 97",
108=>"\\aftnrestart",109=>"\\ftntj",110=>"\\endnotes",111=>"\\hyphconsec",112=>"\\hyphcaps",113=>"\\hyphauto",114=>"\\htmautsp 2000",
115=>"\\horzdoc 7.0",116=>"\\jcompress 7.0",117=>"\\rtlgutter 2000",118=>"\\jexpand 7.0",119=>"\\ftnstart",120=>"\\ftnsepc",
121=>"\\ftnsep",122=>"\\ftnrstpg",123=>"\\ftnrstcont",124=>"\\ftnrestart",125=>"\\gutterprl 7.0",126=>"\\lnbrkrule 2000",
127=>"\\margb",128=>"\\makebackup",129=>"\\lyttblrtgr 2000",130=>"\\lytprtmet 97",131=>"\\lytexcttp 97",132=>"\\lytcalctblwd 2000",
133=>"\\hyphhotz",134=>"\\lnongrid 7.0",135=>"\\ftnnzodiac 97",136=>"\\linkstyles",137=>"\\linestart",138=>"\\lchars 7.0",
139=>"\\landscape",140=>"\\ksulang 2000",141=>"\\jsksu 2000",142=>"\\ltrdoc",143=>"\\formshade",144=>"\\ftnnalc",145=>"\\ftnlytwnine 2000",
146=>"\\ftncn",147=>"\\ftnbj",148=>"\\ftnalt",149=>"\\fromtext 97",150=>"\\ftnnzodiacl 97",151=>"\\fracwidth",152=>"\\ftnnchi",
153=>"\\formprot",154=>"\\formdisp",155=>"\\fldalt",156=>"\\fet",157=>"\\fchars 7.0",158=>"\\margl",159=>"\\fromhtml 97",
160=>"\\ftnndbnumt 97",161=>"\\expshrtn 97",162=>"\\ftnnruc",163=>"\\ftnnrlc",164=>"\\ftnngbnuml 97",165=>"\\ftnngbnumk 97",
166=>"\\ftnngbnumd 97",167=>"\\ftnnar",168=>"\\ftnnganada 97",169=>"\\ftnnauc",170=>"\\ftnndbnumk 97",171=>"\\ftnndbnumd 97",
172=>"\\ftnndbnum 97",173=>"\\ftnndbar 97",174=>"\\ftnncnum 97",175=>"\\ftnnchosung 97",176=>"\\ftnnzodiacd 97",177=>"\\ftnngbnum 97",
178=>"\\viewkind 97",179=>"\\transmf",180=>"\\useltbaln 2000",181=>"\\snaptogridincell 2002",182=>"\\toplinepunct 2002",
183=>"\\vertdoc 7.0",184=>"\\truncatefontheight",185=>"\\viewnobound 2002",186=>"\\splytwnine 2000",187=>"\\sprslnsp 7.0",
188=>"\\sprsspbf",189=>"\\sprstsm 97",190=>"\\sprstsp",191=>"\\template",192=>"\\twoonone 7.0",193=>"\\sprsbsp 97",194=>"\\swpbdr",
195=>"\\subfontbysize 7.0",196=>"\\viewzk 97",197=>"\\widowctrl",198=>"\\windowcaption 97",199=>"\\wpjst 97",200=>"\\wpsp 97",
201=>"\\wraptrsp",202=>"\\wrppunct 2002",203=>"\\truncex",204=>"\\wptab",205=>"\\snapgridtocell"),
"type"=>array("Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Destination","Flag",
"Value","Flag","Value","Flag","Value","Value","Flag","Flag","Destination","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Value","Flag","Flag","Value","Flag","Value","Value","Value",
"Flag","Flag","Flag","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Value","Value","Flag","Flag","Value",
"Value","Flag","Flag","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Destination","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Destination","Destination","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Toggle","Toggle","Flag","Flag","Flag","Flag","Flag","Value",
"Destination","Destination","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag",
"Value","Destination","Flag","Value","Flag","Flag","Flag","Flag","Flag","Destination","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Value","Destination","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Destination","Flag","Flag","Flag","Flag","Value","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag"),
"meaning"=>array()),

"Document Variables"=>array(
"key"=>array(0=>"\\docvar 7.0"),
"type"=>array("Destination"),
"meaning"=>array()),

"Drawing Objects"=>array(
"key"=>array("\\dpcotsingle","\\dpcotright","\\dpcotdouble","\\dpcominusy","\\dpcooffset","\\dpcosmarta","\\dpcottriple","\\dpcount","\\dpellipse",
"\\dpendgroup","\\dpfillbgcb","\\dpcominusx","\\dpfillbgcr","\\dpcobestfit","\\dpfillbggray","\\dpfillbgcg","\\dpcallout","\\dpaendl","\\dpaendsol",
"\\dpaendw","\\dparc","\\dparcflipx","\\dparcflipy","\\dpastarthol","\\dpastartl","\\dpcodabs","\\dpastartw","\\dpcolength","\\dpcoa","\\dpcoaccent",
"\\dplinecob","\\dpcoborder","\\dpfillbgpal","\\dpcodbottom","\\dpcodcenter","\\dpcodescent","\\dpcodtop","\\dpastartsol","\\dptxbxtext",
"\\dpfillpat","\\dppty","\\dprect","\\dproundr","\\dpshadow","\\dpshadx","\\dpshady","\\dptxbtlr 7.0","\\dppolyline","\\dptxbxmar","\\dppolygon",
"\\dptxlrtb 7.0","\\dptxlrtbv 7.0","\\dptxtbrl 7.0","\\dptxtbrlv 7.0","\\dpx","\\dpxsize","\\dpy","\\dpysize","\\dptxbx","\\dplinedadodo",
"\\dpfillfgcg","\\dpfillfgcr","\\dpfillfggray","\\dpfillfgpal","\\dpaendhol","\\dpline","\\dpgroup","\\dplinecog","\\dpptx","\\dplinedado",
"\\dpfillfgcb","\\dplinedash","\\dplinedot","\\dplinegray","\\dplinehollow","\\dplinepal","\\dplinesolid","\\dplinew","\\dppolycount",
"\\dplinecor","\\dobxmargin","\\dobypage","\\dobypara","\\dobxpage","\\dobymargin","\\dodhgt","\\dolock","\\dobxcolumn","\\do"),
"type"=>array("Flag","Flag","Flag","Flag","Value","Flag","Flag","Value","Flag","Flag","Value","Flag","Value","Flag","Value","Value",
"Flag","Value","Flag","Value","Flag","Flag","Flag","Flag","Value","Value","Value","Value","Value","Flag","Value","Flag","Flag","Flag",
"Flag","Value","Flag","Flag","Destination","Value","Value","Flag","Flag","Flag","Value","Value","Flag","Flag","Value","Flag","Flag",
"Flag","Flag","Flag","Value","Value","Value","Value","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Value","Value",
"Flag","Value","Flag","Flag","Value","Flag","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag",
"Destination"),
"meaning"=>array()),

"Fields"=>array(
"key"=>array(0=>"\\field",1=>"\\flddirty",2=>"\\fldtype 97",3=>"\\datafield",4=>"\\date 97",5=>"\\fldedit",6=>"\\fldinst",7=>"\\fldpriv",
8=>"\\time 97",9=>"\\fldlock",10=>"\\fldrslt",11=>"\\wpeqn 97",12=>"\\fldalt"),
"type"=>array("Destination","Flag","Destination","Destination","Flag","Flag","Destination","Flag","Flag","Flag","Destination","Flag"),
"meaning"=>array()),

"File Table"=>array(
"key"=>array("\\fnonfilesys 2002","\\fosnum","\\frelative","\\fnetwork","\\file","\\fvalidntfs","\\fvalidhpfs","\\fvaliddos","\\fid","\\filetbl",
"\\fvalidmac"),
"type"=>array("Flag","Value","Value","Flag","Destination","Flag","Flag","Flag","Value","Destination","Flag"),
"meaning"=>array()),

"Font (Character) Formatting Properties"=>array(
"key"=>array(0=>"\\cs",1=>"\\up",2=>"\\ulwave 7.0",3=>"\\rtlch",4=>"\\langnp 2000",5=>"\\cf",6=>"\\plain",7=>"\\ulthdashd 2000",
8=>"\\revised",9=>"\\ululdbwave 2000",10=>"\\ulthldash 2000",11=>"\\ulthdashdd 2000",12=>"\\cchs",13=>"\\deleted",14=>"\\fittext 2000",
15=>"\\ulw",16=>"\\cgrid 97",17=>"\\uldb",18=>"\\noproof 2000",19=>"\\nosectexpand 97",20=>"\\nosupersub",21=>"\\uldashdd 7.0",22=>"\\sub",
23=>"\\uldashd 7.0",24=>"\\uldash 7.0",25=>"\\uld",26=>"\\ulc 2000",27=>"\\ul",28=>"\\expnd",29=>"\\charscalex 7.0",30=>"\\ulthdash 2000",
31=>"\\b",32=>"\\ltrch",33=>"\\ulhair 7.0",34=>"\\ulhwave 2000",35=>"\\ulldash 2000",36=>"\\ulnone",37=>"\\ulth 7.0",38=>"\\strike",
39=>"\\outl",40=>"\\ulthd 2000",41=>"\\f",42=>"\\expndtw",43=>"\\animtext 97",44=>"\\accdot 7.0",45=>"\\g 97",46=>"\\gcw 97",
47=>"\\accnone 7.0",48=>"\\dn",49=>"\\caps",50=>"\\lang",51=>"\\cb",52=>"\\langfenp 2000",53=>"\\langfe 2000",54=>"\\acccomma 7.0",
55=>"\\scaps",56=>"\\kerning",57=>"\\i",58=>"\\revauth",59=>"\\fs",60=>"\\revdttm",61=>"\\shad",62=>"\\gridtbl 97",63=>"\\super",
64=>"\\v",65=>"\\webhidden 2000"),
"type"=>array("Value","Value","Toggle","Flag","Value","Value","Flag","Toggle","Toggle","Toggle","Toggle","Toggle","Value","Toggle","Value",
"Flag","Value","Toggle","Flag","Flag","Flag","Toggle","Flag","Toggle","Toggle","Flag","Value","Toggle","Value","Value","Toggle","Toggle",
"Flag","Toggle","Toggle","Toggle","Flag","Toggle","Toggle","Toggle","Toggle","Value","Value","Value","Toggle","Destination","Value",
"Toggle","Value","Toggle","Value","Value","Value","Value","Toggle","Toggle","Value","Toggle","Value","Value","Value","Toggle",
"Destination","Flag","Toggle","Flag"),
"meaning"=>array()),

"Font Table"=>array(
"key"=>array(0=>"\\fetch",1=>"\\deff",2=>"\\fname 7.0",3=>"\\fcharset",4=>"\\fscript",5=>"\\fonttbl",6=>"\\fontfile",7=>"\\fontemb",8=>"\\fbidi",
9=>"\\fttruetype",10=>"\\fnil",11=>"\\ftnil",12=>"\\fdecor",13=>"\\fswiss",14=>"\\falt",15=>"\\panose 97",16=>"\\froman",17=>"\\fbiasN 97",18=>"\\fmodern",
19=>"\\fprq"),
"type"=>array("Flag","Value","Destination","Value","Flag","Destination","Destination","Destination","Flag","Flag","Flag","Flag","Flag",
"Flag","Destination","Destination","Flag","Value","Flag","Value"),
"meaning"=>array()),

"Footnotes"=>array(
"key"=>array(0=>"\\footnote"),
"type"=>array("Destination"),
"meaning"=>array()),

"Form Fields"=>array(
"key"=>array("\\ffhpsN 97","\\fftypeN 97","\\ffl 97","\\ffprotN 97","\\ffresN 97","\\ffformat 97","\\ffrecalcN 97","\\ffhelptext 97",
"\\ffownstatN 97","\\ffsizeN 97","\\ffexitmcr 97","\\ffdefres 97","\\ffentrymcr 97","\\fftypetxtN 97","\\ffdeftext 97","\\ffownhelpN 97",
"\\ffname 97","\\ffmaxlen 97","\\formfield 97","\\ffhaslistboxN 97","\\ffstattext 97"),
"type"=>array("Value","Value","Destination","Value","Value","Destination","Value","Destination","Value","Value","Destination","Value",
"Destination","Value","Destination","Value","Destination","Value","Destination","Value","Destination"),
"meaning"=>array()),

"Generator"=>array(
"key"=>array(0=>"\\generator 2002"),
"type"=>array("Destination"),
"meaning"=>array()),

"Headers and Footers"=>array(
"key"=>array(0=>"\\footerl",1=>"\\headerf",2=>"\\footer",3=>"\\headerl",4=>"\\header",5=>"\\footerf",6=>"\\headerr",7=>"\\footerr"),
"type"=>array("Destination","Destination","Destination","Destination","Destination","Destination","Destination","Destination"),
"meaning"=>array()),

"Highlighting"=>array(
"key"=>array(0=>"\\highlight 7.0"),
"type"=>array("Value"),
"meaning"=>array()),

"Index Entries"=>array(
"key"=>array("\\ixe","\\rxe","\\pxe 7.0","\\txe","\\bxe","\\xe","\\xef","\\yxe 97"),
"type"=>array("Flag","Destination","Destination","Destination","Flag","Destination","Value","Flag"),
"meaning"=>array()),

"Information Group"=>array(
"key"=>array(0=>"\\nofchars",1=>"\\id",2=>"\\keywords",3=>"\\nofwords",4=>"\\hr",5=>"\\nofpages",6=>"\\subject",7=>"\\printim",
8=>"\\nofcharsws 97",9=>"\\mo",10=>"\\sec",11=>"\\operator",12=>"\\min",13=>"\\author",14=>"\\manager 7.0",15=>"\\hlinkbase 97",16=>"\\dy",
17=>"\\vern",18=>"\\version",19=>"\\info",20=>"\\category 7.0",21=>"\\creatim",22=>"\\title",23=>"\\edmins",24=>"\\linkval 7.0",
25=>"\\userprops 7.0",26=>"\\doccomm",27=>"\\revtim",28=>"\\proptype 7.0",29=>"\\company 7.0",30=>"\\comment",31=>"\\staticval 7.0",
32=>"\\propname 7.0",33=>"\\buptim",34=>"\\yr"),
"type"=>array("Value","Value","Destination","Value","Value","Value","Destination","Destination","Value","Value","Value","Destination",
"Value","Destination","Destination","Value","Value","Value","Value","Destination","Destination","Destination","Destination","Value","Value",
"Destination","Destination","Destination","Value","Destination","Destination","Value","Value","Destination","Value"),
"meaning"=>array()),

"List Table"=>array(
"key"=>array("\\levelprevspaceN 97","\\levelprevN 97","\\levelpictureN 2002","\\leveloldN 97","\\levelnumbers 97","\\levelnorestartN 97",
"\\levelnfcN 97","\\leveltemplateidN 2000","\\levellegalN 97","\\leveljcnN 2000","\\leveljcN 97","\\levelindentN 97","\\levelfollowN 97",
"\\liststyleidN 2002","\\levelnfcnN 2000","\\listoverridestartN 97","\\ls 97","\\listidN 97","\\listname 97","\\listtemplateidN 97",
"\\listoverridecountN 97","\\liststylename 2002","\\levelspaceN 97","\\listoverrideformatN 97","\\levelstartatN 97","\\listpictureN 2002",
"\\listrestarthdnN 97","\\listsimpleN 97","\\leveltext 97","\\listhybrid 2000"),
"type"=>array("Value","Value","Value","Value","Destination","Value","Value","Value","Value","Value","Value","Value","Value","Value","Value",
"Value","Value","Value","Destination","Value","Value","Value","Value","Value","Value","Value","Value","Value","Value","Flag"),
"meaning"=>array()),

"Macintosh Edition Manager Publisher Objects"=>array(
"key"=>array("\\pubauto","\\bkmkpub"),
"type"=>array("Flag","Flag"),
"meaning"=>array()),

"Objects"=>array(
"key"=>array("\\objw","\\linkself","\\objsect","\\objicemb","\\objlink","\\objlock","\\objname","\\objocx 97","\\objpub","\\rsltrtf","\\objscalex",
"\\objhtml 97","\\objscaley","\\rslttxt","\\objsetsize","\\rsltmerge","\\rslthtml 2000","\\objsub","\\rsltbmp","\\objtime","\\objupdate","\\result",
"\\rsltpict","\\objautlink","\\objh","\\objalias","\\objalign","\\objattph 7.0","\\objtransy","\\objclass","\\objcropb","\\objcropt","\\objcropl",
"\\object","\\objemb","\\objdata","\\objcropr"),
"type"=>array("Value","Flag","Destination","Flag","Flag","Flag","Destination","Flag","Flag","Flag","Value","Flag","Value","Flag","Flag",
"Flag","Flag","Flag","Flag","Destination","Flag","Destination","Flag","Flag","Value","Destination","Value","Flag","Value","Destination",
"Value","Value","Value","Destination","Flag","Destination","Value"),
"meaning"=>array()),

"Paragraph Borders"=>array(
"key"=>array(0=>"\\brdrdot",1=>"\\box",2=>"\\brdrr",3=>"\\brdrframe 97",4=>"\\brdrsh",5=>"\\brdrdb",6=>"\\brdrnil 2002",7=>"\\brdrl",
8=>"\\brdrcf",9=>"\\brdrt",10=>"\\brdrs",11=>"\\brdrdash",12=>"\\brdrtbl 2002",13=>"\\brdrhair",14=>"\\brdrb",15=>"\\brdrth",16=>"\\brsp",
17=>"\\brdrbtw",18=>"\\brdrbar",19=>"\\brdrw"),
"type"=>array("Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value",
"Flag","Flag","Value"),
"meaning"=>array()),

"Paragraph Formatting Properties"=>array(
"key"=>array(0=>"\\li",1=>"\\keepn",2=>"\\qj",3=>"\\qd 7.0",4=>"\\qc",5=>"\\fi",6=>"\\sbys",7=>"\\itap 2000",8=>"\\intbl",9=>"\\sb",10=>"\\hyphpar",
11=>"\\sl",12=>"\\faroman 7.0",13=>"\\level",14=>"\\fafixed 7.0",15=>"\\noline",16=>"\\nowwrap 7.0",17=>"\\fahang 7.0",18=>"\\ltrpar",19=>"\\culi 2000",
20=>"\\cufi 2000",21=>"\\rtlpar",22=>"\\s",23=>"\\sa",24=>"\\favar 7.0",25=>"\\aspalpha 7.0",26=>"\\rin 2000",27=>"\\nocwrap 7.0",28=>"\\saauto 2000",
29=>"\\nowidctlpar",30=>"\\subdocument",31=>"\\nosnaplinegrid 97",32=>"\\nooverflow 7.0",33=>"\\collapsed",34=>"\\aspnum 7.0",35=>"\\lisb 2000",
36=>"\\qk 2002",37=>"\\ql",38=>"\\qr",39=>"\\qt 2002",40=>"\\curi 2000",41=>"\\lisa 2000",42=>"\\slmult",43=>"\\spv 2002",44=>"\\faauto 97",
45=>"\\facenter 7.0",46=>"\\pard",47=>"\\keep",48=>"\\pagebb",49=>"\\sbauto 2000",50=>"\\ri",51=>"\\lin 2000",52=>"\\widctlpar",53=>"\\yts 2002"),
"type"=>array("Value","Flag","Flag","Flag","Flag","Value","Flag","Value","Flag","Value","Toggle","Value","Flag","Value","Flag","Flag","Flag",
"Flag","Flag","Value","Value","Flag","Value","Value","Flag","Toggle","Value","Flag","Toggle","Flag","Value","Flag","Flag","Flag","Toggle",
"Value","Flag","Flag","Flag","Flag","Value","Value","Value","Flag","Value","Flag","Flag","Flag","Flag","Toggle","Value","Value","Flag","Value"),
"meaning"=>array()),

"Paragraph Group Properties"=>array(
"key"=>array(0=>"\\pgptbl 2002",1=>"\\pgp 2002",2=>"\\ipgpN 2002"),
"type"=>array("Destination","Destination","Value"),
"meaning"=>array()),

"Paragraph Shading"=>array(
"key"=>array(0=>"\\cfpat",1=>"\\cbpat",2=>"\\bghoriz",3=>"\\bgfdiag",4=>"\\bgdkvert",5=>"\\bgdkhoriz",6=>"\\bgdkfdiag",7=>"\\bgdkdcross",
8=>"\\bgdkcross",9=>"\\bgdkbdiag",10=>"\\bgdcross",11=>"\\bgcross",12=>"\\bgbdiag",13=>"\\bgvert",14=>"\\shading"),
"type"=>array("Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value"),
"meaning"=>array()),

"Paragraph Text"=>array(
"key"=>array(0=>"\\brdrtnthsg 97",1=>"\\outlinelevel 97",2=>"\\brdrtnthtnmg 97",3=>"\\brdremboss 97",4=>"\\brdrengrave 97",5=>"\\posyin 97",
6=>"\\brdrtnthtnlg 97",7=>"\\brdrthtnlg 97",8=>"\\pnrxst 97",9=>"\\pnrrgb 97",10=>"\\listtext 97",11=>"\\pnrpnbr 97",12=>"\\brdrtnthmg 97",
13=>"\\brdrtnthlg 97",14=>"\\dfrauth 97",15=>"\\brdrthtnsg 97",16=>"\\brdrthtnmg 97",17=>"\\posyout 97",18=>"\\brdroutset 2000",
19=>"\\overlay 97",20=>"\\dfrstart 97",21=>"\\brdrwavy 97",22=>"\\dfrstop 97",23=>"\\ilvl 97",24=>"\\dfrxst 97",25=>"\\pnrstop 97",
26=>"\\pnrstart 97",27=>"\\brdrdashd 97",28=>"\\brdrinset 2000",29=>"\\pnrauth 97",30=>"\\brdrdashdd 97",31=>"\\brdrdashdotstr 97",
32=>"\\brdrdashsm 97",33=>"\\dfrdate 97",34=>"\\brdrtriple 97",35=>"\\pnrdate 97",36=>"\\brdrtnthtnsg 97",37=>"\\pnrnfc 97",
38=>"\\pnrnot 97",39=>"\\brdrwavydb 97"),
"type"=>array("Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Value","Value","Destination","Value","Flag","Flag","Value","Flag",
"Flag","Flag","Flag","Flag","Value","Flag","Value","Value","Value","Value","Value","Flag","Flag","Value","Flag","Flag","Flag","Value",
"Flag","Value","Flag","Value","Flag","Flag"),
"meaning"=>array()),

"Pictures"=>array(
"key"=>array(0=>"\\emfblip 97",1=>"\\dibitmap",2=>"\\bliptag 97",3=>"\\jpegblip 97",4=>"\\bin",5=>"\\blipuid 97",6=>"\\pngblip 97",
7=>"\\nonshppict 97",8=>"\\macpict",9=>"\\shppict 97",10=>"\\pmmetafile",11=>"\\picwgoal",12=>"\\piccropr",13=>"\\blipupi 97",
14=>"\\picbmp",15=>"\\pict",16=>"\\picscaley",17=>"\\picscaled",18=>"\\defshp 2000",19=>"\\picprop 97",20=>"\\pichgoal",21=>"\\picbpp",
22=>"\\piccropb",23=>"\\pich",24=>"\\picscalex",25=>"\\picw",26=>"\\piccropl",27=>"\\piccropt",28=>"\\wbitmap",29=>"\\wbmbitspixel",
30=>"\\wbmplanes",31=>"\\wbmwidthbytes",32=>"\\wmetafile"),
"type"=>array("Flag","Value","Value","Flag","Value","Value","Flag","Flag","Flag","Destination","Value","Value","Value","Value","Flag",
"Destination","Value","Flag","Flag","Destination","Value","Value","Value","Value","Value","Value","Value","Value","Value","Value","Value","Value",
"Value"),
"meaning"=>array()),

"Positioned Objects and Frames"=>array(
"key"=>array(0=>"\\nowrap",1=>"\\frmtxtbrlv 7.0",2=>"\\frmtxtbrl 7.0",3=>"\\frmtxlrtbv 7.0",4=>"\\frmtxbtlr 7.0",5=>"\\dfrmtxty",
6=>"\\dfrmtxtx",7=>"\\frmtxlrtb 7.0",8=>"\\posyc",9=>"\\posnegx",10=>"\\posnegy",11=>"\\posx",12=>"\\posxc",13=>"\\posxi",14=>"\\posxl",
15=>"\\posxo",16=>"\\posxr",17=>"\\phcol",18=>"\\posyb",19=>"\\posyil",20=>"\\posyt",21=>"\\dxfrtext",22=>"\\absh",23=>"\\abslock 7.0",
24=>"\\absnoovrlp 2000",25=>"\\posy",26=>"\\absw",27=>"\\pvmrg",28=>"\\dropcapt",29=>"\\dropcapli",30=>"\\phmrg",31=>"\\phpg",32=>"\\pvpg",
33=>"\\pvpara",34=>"\\posyin 97",35=>"\\posyout 97",36=>"\\overlay 97"),
"type"=>array("Flag","Flag","Flag","Flag","Flag","Value","Value","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Flag","Value","Value","Flag","Toggle","Value","Value","Flag","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag"),
"meaning"=>array()),

"RTF Version"=>array(
"key"=>array(0=>"\\rtf"),
"type"=>array("Destination"),
"meaning"=>array()),

"Section Formatting Properties"=>array(
"key"=>array(0=>"\\endnhere",1=>"\\colw",2=>"\\colsx",3=>"\\colsr",4=>"\\cols",5=>"\\colno",6=>"\\ds",7=>"\\sftnndbnumk 2002",8=>"\\sftnnzodiacd 2002",
9=>"\\sftnnzodiac 2002",10=>"\\sftnnruc 2002",11=>"\\sftnnrlc 2002",12=>"\\sftnngbnuml 2002",13=>"\\sftnngbnumk 2002",14=>"\\sftnngbnumd 2002",
15=>"\\pgnthaia 2002",16=>"\\linerestart",17=>"\\sftnrstcont 2002",18=>"\\sftnndbnumd 2002",19=>"\\sftnndbnum 2002",20=>"\\sftnndbar 2002",
21=>"\\sftnncnum 2002",22=>"\\sftnnchosung 2002",23=>"\\sftnnchi 2002",24=>"\\sftnnauc 2002",25=>"\\sftnnganada 2002",26=>"\\vertalc",27=>"\\pgnthaib 2002",
28=>"\\pgnthaic 2002",29=>"\\pgnucltr",30=>"\\pgnucrm",31=>"\\pgnvieta 2002",32=>"\\pgnx",33=>"\\pgny",34=>"\\sftnnzodiacl 2002",35=>"\\vertalb",
36=>"\\sftnrestart 2002",37=>"\\vertalj",38=>"\\vertalt",39=>"\\adjustright 97",40=>"\\vertsect 7.0",41=>"\\sftntj 2002",42=>"\\sftnstart 2002",
43=>"\\sftnrstpg 2002",44=>"\\sftnbj 2002",45=>"\\pgwsxn",46=>"\\rtlsect",47=>"\\sftnnar 2002",48=>"\\saftnnrlc 2002",49=>"\\saftnngbnuml 2002",
50=>"\\saftnngbnumk 2002",51=>"\\saftnngbnumd 2002",52=>"\\saftnngbnum 2002",53=>"\\saftnnganada 2002",54=>"\\saftnnzodiac 2002",55=>"\\saftnndbnumk 2002",
56=>"\\saftnnzodiacd 2002",57=>"\\saftnnalc 2002",58=>"\\saftnnar 2002",59=>"\\saftnnauc 2002",60=>"\\saftnnchi 2002",61=>"\\saftnnchosung 2002",
62=>"\\saftnncnum 2002",63=>"\\saftnndbar 2002",64=>"\\saftnndbnum 2002",65=>"\\saftnndbnumt 2002",66=>"\\sbkpage",67=>"\\sftnngbnum 2002",
68=>"\\sectunlocked",69=>"\\sectspecifyl 97",70=>"\\sectspecifygenN",71=>"\\sectspecifycl 97",72=>"\\sectlinegrid 97",73=>"\\sectexpand 97",
74=>"\\saftnnruc 2002",75=>"\\sectd",76=>"\\sftnnalc 2002",77=>"\\sbkodd",78=>"\\sbknone",79=>"\\sbkeven",80=>"\\sbkcol",81=>"\\saftnstart 2002",
82=>"\\saftnrstcont 2002",83=>"\\saftnrestart 2002",84=>"\\saftnnzodiacl 2002",85=>"\\sectdefaultcl 97",86=>"\\linecont",87=>"\\pgnbidia 2000",88=>"\\pghsxn",
89=>"\\margrsxn",90=>"\\marglsxn",91=>"\\margbsxn",92=>"\\ltrsect",93=>"\\lndscpsxn",94=>"\\linex",95=>"\\pgnstarts",96=>"\\lineppage",97=>"\\pgnbidib 2000",
98=>"\\linemod",99=>"\\linestarts",100=>"\\linebetcol",101=>"\\titlepg",102=>"\\binfsxn",103=>"\\binsxn",104=>"\\horzsect 7.0",105=>"\\headery",
106=>"\\guttersxn",107=>"\\saftnndbnumd 2002",108=>"\\margtsxn",109=>"\\footery",110=>"\\sftnndbnumt 2002",111=>"\\pgnhnsm ",112=>"\\pgnhindid 2002",
113=>"\\pgnhnsc ",114=>"\\pgncont",115=>"\\pgnhindic 2002",116=>"\\pgnhindib 2002",117=>"\\pgnrestart",118=>"\\pgndec",119=>"\\pgndecd 7.0",120=>"\\pgnhn",
121=>"\\pgndbnumd 7.0",122=>"\\pgndbnum 7.0",123=>"\\pgnhnsn",124=>"",125=>"\\pgnlcltr",126=>"\\pgnlcrm",127=>"\\pgnhnsp",128=>"\\pgnid 2002",
129=>"\\pgnhindia 2002",130=>"\\pgnhnsh",131=>"\\margmirsxn",132=>"\\pgnchosung 97",133=>"\\pgncnum 97",134=>"\\pgndbnumt 97",135=>"\\pgndbnumk 97",
136=>"\\pgnganada 97",137=>"\\pgngbnum 97",138=>"\\pgngbnumd 97",139=>"\\pgngbnuml 97",140=>"\\pgngbnumk 97",141=>"\\pgnzodiac 97",
142=>"\\pgnzodiacd 97",143=>"\\pgnzodiacl 97",144=>"\\stextflow 97"),
"type"=>array("Flag","Value","Value","Value","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Value","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Value","Value",
"Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Value","Value","Value","Value",
"Flag","Flag","Value","Value","Flag","Flag","Value","Value","Flag","Flag","Value","Value","Flag","Value","Value","Flag","Value","Value",
"Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag",
"Flag","Flag"),
"meaning"=>array()),

"Section Text"=>array(
"key"=>array(0=>"\\stextflow 97"),
"type"=>array("Value"),
"meaning"=>array()),

"Special Characters"=>array(
"key"=>array(0=>"\\|",1=>"\{",2=>"\}",3=>"\\rdblquote",4=>"\\qmspace 7.0",5=>"\\line",6=>"\\ldblquote",7=>"\\lbrN 2000",8=>"\\chtime",9=>"\\row",
10=>"\\rquote",11=>"\\\\",12=>"\\:",13=>"\\*",14=>"\\rtlmark 2002",15=>"\\-",16=>"\\cell",17=>"\\bullet",18=>"\\column",19=>"\\_",20=>"\\chdpa",
21=>"\\'",22=>"\\tab",23=>"\\~",24=>"\\page",25=>"\\par",26=>"\\chatn",27=>"\\chdate",28=>"\\softcol",29=>"\\enspace",30=>"\\sectnum",
31=>"\\chdpl",32=>"\\endash",33=>"\\emspace",34=>"\\softpage",35=>"\\emdash",36=>"\\chftn",37=>"\\softline",38=>"\\softlheight",39=>"\\chftnsep",
40=>"\\chpgn",41=>"\\lquote",42=>"\\chftnsepc",43=>"\\ltrmark 2002",44=>"\\sect",45=>"\\zwbo 7.0",46=>"\\zwj 2002",47=>"\\zwnbo 7.0",
48=>"\\zwnj 2002"),
"type"=>array("Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol",
"Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Flag",
"Symbol","Symbol","Symbol","Symbol","Symbol","Flag","Symbol","Symbol","Flag","Value","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol","Symbol",
"Symbol","Symbol","Symbol"),
"meaning"=>array()),

"Style Sheet"=>array(
"key"=>array(0=>"\\stylesheet",1=>"\\sreply 2000",2=>"\\spersonal 2000",3=>"\\sbasedon",4=>"\\ssemihidden 2002",5=>"\\additive",6=>"\\fn",
7=>"\\shift",8=>"\\shidden 97",9=>"\\sautoupd 97",10=>"\\ctrl",11=>"\\ts 2002",12=>"\\tsrowd 2002",13=>"\\alt",14=>"\\scompose 2000",
15=>"\\keycode",16=>"\\snext"),
"type"=>array("Destination","Flag","Flag","Value","Flag","Flag","Value","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag",
"Destination","Value"),
"meaning"=>array()),

"Table Definitions"=>array(
"key"=>array(0=>"\\clbgdkbdiag",1=>"\\clpadfrN 2000",2=>"\\clpadflN 2000",3=>"\\clpadfbN 2000",4=>"\\clpadbN 2000",5=>"\\clpadtN 2000",
6=>"\\clNoWrap 2000",7=>"\\cldgll 7.0",8=>"\\clpadlN 2000",9=>"\\clpadrN 2000",10=>"\\cldglu 7.0",11=>"\\clpadftN 2000",12=>"\\clftsWidthN 2000",
13=>"\\clmgf",14=>"\\cellx",15=>"\\clmrg",16=>"\\clFitText 2000",17=>"\\clbghoriz",18=>"\\clbgbdiag",19=>"\\clbgcross",20=>"\\clbgdcross",
21=>"\\clbgdkcross",22=>"\\clshdng",23=>"\\clbgdkfdiag",24=>"\\clbgdkhor",25=>"\\clbgdkdcross",26=>"\\clbgfdiag",27=>"\\clcfpatrawN 2002",
28=>"\\clbgvert",29=>"\\clbrdrb",30=>"\\clbrdrl",31=>"\\clbrdrr",32=>"\\clbrdrt",33=>"\\clcbpat",34=>"\\clcbpatrawN 2002",35=>"\\clcfpat",
36=>"\\clbgdkvert",37=>"\\tpvpg 2000",38=>"\\trftsWidthAN 2000",39=>"\\trbgcross 2002",40=>"\\trbgbdiag 2002",41=>"\\trautofitN 2000",
42=>"\\trauthN 2002",43=>"\\trbgdkbdiag 2002",44=>"\\tbllkborder 2002",45=>"\\trbgdkcross 2002",46=>"\\tpvpara 2000",47=>"\\tpvmrg 2000",
48=>"\\tposyt 2000",49=>"\\tposyoutv 2000",50=>"\\tposyin 2000",51=>"\\tposyil 2000",52=>"\\nestcell 2000",53=>"\\trbrdrh",54=>"\\trdateN",
55=>"\\trcfpatN 2002",56=>"\\trcbpatN 2002",57=>"\\trbrdrv",58=>"\\trbrdrt",59=>"\\trbgdcross 2002",60=>"\\trbrdrl",61=>"\\tposy 2000",
62=>"\\trbrdrb",63=>"\\trbghoriz 2002",64=>"\\trbgdkvert 2002",65=>"\\trbgdkhor 2002",66=>"\\trbgdkfdiag 2002",67=>"\\trbgdkdcross 2002",
68=>"\\trbrdrr",69=>"\\tdfrmtxtLeftN 2000",70=>"\\irowN 2002",71=>"\\irowbandN 2002",72=>"\\tbllkhdrrows 2002",73=>"\\tbllklastcol 2002",
74=>"\\lastrow 2002",75=>"\\tposyc 2000",76=>"\\tdfrmtxtRightN 2000",77=>"\\tbllkhdrcols 2002",78=>"\\tdfrmtxtBottomN 2000",79=>"\\ltrrow",
80=>"\\tcelld 97",81=>"\\tblrsidN 2002",82=>"\\tbllkshading 2002",83=>"\\tbllklastrow 2002",84=>"\\tdfrmtxtTopN 2000",85=>"\\tposxc 2000",
86=>"\\trbgfdiag 2002",87=>"\\tposxr 2000",88=>"\\tposxo 2000",89=>"\\tposxN 2000",90=>"\\tposxl 2000",91=>"\\tposxi 2000",92=>"\\trbgvert 2002",
93=>"\\tbllkfont 2002",94=>"\\clshdngraw 2002",95=>"\\tposnegyN 2000",96=>"\\tposnegxN 2000",97=>"\\tphpg 2000",98=>"\\tphmrg 2000",
99=>"\\tphcol 2000",100=>"\\tposyb 2000",101=>"\\tbllkcolor 2002",102=>"\\trspdbN 2000",103=>"\\trpaddtN 2000",104=>"\\trspdrN 2000",
105=>"\\trspdlN 2000",106=>"\\trspdftN 2000",107=>"\\nestrow 2000",108=>"\\trspdflN 2000",109=>"\\trwWidthAN 2000",110=>"\\rtlrow",
111=>"\\trwWidthBN 2000",112=>"\\trshdngN 2002",113=>"\\trrh",114=>"\\trqr",115=>"\\trql",116=>"\\trqc",117=>"\\trpatN 2002",
118=>"\\trftsWidthBN 2000",119=>"\\clvertalb 7.0",120=>"\\clshdrawnil 2002",121=>"\\cltxbtlr 7.0",122=>"\\cltxlrtb 7.0",123=>"\\cltxlrtb 97",
124=>"\\cltxlrtbv 7.0",125=>"\\cltxtbrl 97",126=>"\\trspdtN 2000",127=>"\\cltxtbrlv 7.0",128=>"\\trspdfbN 2000",129=>"\\clvertalc 7.0",
130=>"\\clvertalt 7.0",131=>"\\clvmgf 7.0",132=>"\\clvmrg 7.0",133=>"\\clwWidthN 2000",134=>"\\trwWidthN 2000",135=>"\\cltxtbrl 7.0",
136=>"\\trgaph",137=>"\\rawbgdkbdiag 2002",138=>"\\trftsWidthN 2000",139=>"\\rawclbgdkdcross 2002",140=>"\\rawclbgdkcross 2002",
141=>"\\trpaddrN 2000",142=>"\\rawclbgdkhor 2002",143=>"\\trspdfrN 2000",144=>"\\nesttableprops 2000",145=>"\\taprtl 2000",
146=>"\\rawclbgdcross 2002",147=>"\\rawclbgcross 2002",148=>"\\rawclbgbdiag 2002",149=>"\\nonesttables 2000",150=>"\\tabsnoovrlp 2000",
151=>"\\tbllkbestfit 2002",152=>"\\trleft",153=>"\\trpaddlN 2000",154=>"\\trpaddftN 2000",155=>"\\trpaddfrN 2000",156=>"\\trpaddflN 2000",
157=>"\\trpaddfbN 2000",158=>"\\trpaddbN 2000",159=>"\\rawclbgdkfdiag 2002",160=>"\\trowd",161=>"\\rawclbgdkvert 2002",162=>"\\trkeep",
163=>"\\trhdr",164=>"\\rawclbgvert 2002",165=>"\\rawclbghoriz 2002",166=>"\\rawclbgfdiag 2002"),
"type"=>array("Flag","Value","Value","Value","Value","Value","Flag","Flag","Value","Value","Flag","Value","Value","Flag","Value","Flag",
"Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Value","Value",
"Value","Flag","Flag","Value","Flag","Flag","Toggle","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Symbol",
"Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Value","Value",
"Flag","Flag","Flag","Flag","Value","Flag","Value","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Value","Flag",
"Flag","Flag","Flag","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Value","Value","Value","Value","Value","Symbol","Value",
"Value","Flag","Value","Value","Value","Flag","Flag","Flag","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag",
"Value","Flag","Flag","Flag","Flag","Value","Value","Flag","Value","Flag","Value","Flag","Flag","Value","Flag","Value","Destination","Flag",
"Flag","Flag","Flag","Destination","Flag","Flag","Value","Value","Value","Value","Value","Value","Value","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag"),
"meaning"=>array()),

"Table of Contents Entries"=>array(
"key"=>array("\\tc","\\tcf","\\tcl","\\tcn"),
"type"=>array("Destination","Value","Value","Flag"),
"meaning"=>array()),

"Table Styles"=>array(
"key"=>array("\\tscellwidthfts 2002","\\tscellwidth 2002","\\tscellpaddlN 2002","\\tscellpctN 2002","\\tscellpaddtN 2002","\\tscellpaddrN 2002",
"\\tscfirstcol 2002","\\tscfirstrow 2002","\\tsclastcol 2002","\\tsclastrow 2002","\\tsbrdrt 2002","\\tscnecell 2002","\\tscnwcell 2002",
"\\tscsecell 2002","\\tscswcell 2002","\\tsd 2002","\\tsnowrap 2002","\\tsvertalb 2002","\\tsvertalc 2002","\\tsvertalt 2002",
"\\tscellpaddftN 2002","\\tsbrdrb 2002","\\tsbrdrl 2002","\\tsbrdrr 2002","\\tsbgdkhor 2002","\\tsbgdkfdiag 2002","\\tsbrdrr 2002",
"\\tscellpaddfrN 2002","\\tsbgdkcross 2002","\\tsbgdkvert 2002","\\tsbrdrdgl 2002","\\tsbrdrdgr 2002","\\tsbrdrh 2002","\\tsbgdkbdiag 2002",
"\\tsbgcross 2002","\\tsbgdcross 2002","\\tsbgdkdcross 2002","\\tsbghoriz 2002","\\tscellpaddflN 2002","\\tscellpaddfbN 2002",
"\\tscellpaddbN 2002","\\tscellcfpatN 2002","\\tsbgvert 2002","\\tsbrdrv 2002","\\tscbandvertodd 2002","\\tsbgbdiag 2002","\\tsbgfdiag 2002",
"\\tscbandhorzeven 2002","\\tscbandverteven 2002","\\tscbandsv 2002","\\tscbandsh 2002","\\tscbandhorzodd 2002","\\tscellcbpatN 2002"),
"type"=>array("Flag","Flag","Value","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag",
"Flag","Flag","Value","Value","Value","Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value"),
"meaning"=>array()),

"Tabs"=>array(
"key"=>array(0=>"\\tb",1=>"\\tlul",2=>"\\tldot",3=>"\\tleq",4=>"\\tlhyph",5=>"\\tlmdot 7.0",6=>"\\tlth",7=>"\\tqr",8=>"\\tqc",9=>"\\tqdec",
10=>"\\tx"),
"type"=>array("Value","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Flag","Value"),
"meaning"=>array()),

"Track Changes (Revision Marks)"=>array(
"key"=>array(0=>"\\revtbl",1=>"\\rsid 2002",2=>"\\oldtprops 2002",3=>"\\rsidtbl 2002",4=>"\\sectrsid 2002",5=>"\\delrsid 2002",6=>"\\rsidroot 2002",
7=>"\\charrsid 2002",8=>"\\pararsid 2002",9=>"\\oldsprops 2002",10=>"\\insrsid 2002",11=>"\\oldpprops 2002",12=>"\\oldcprops 2002",13=>"\\styrsid 2002"),
"type"=>array("Destination","Value","Destination","Destination","Value","Value","Value","Value","Value","Destination","Value",
"Destination","Destination","Value"),
"meaning"=>array()),

"Unicode RTF"=>array(
"key"=>array(0=>"\\uc 97",1=>"\\ansicpg 97",2=>"\\upr 97",3=>"\\u 97",4=>"\\ud 97"),
"type"=>array("Value","Value","Destination","Value","Destination"),
"meaning"=>array()),

"Word 97 through Word 2002 RTF for Drawing Objects (Shapes)"=>array(
"key"=>array("0=>\\hlfr 97",1=>"\\shpbottom 97",2=>"\\hlloc 97",3=>"\\shpz 97",4=>"\\hlsrc 97",5=>"\\background 97",6=>"\\shpwr 97",
7=>"\\shpwrk 97",8=>"\\shpbypage 97",9=>"\\shpright 97",10=>"\\shplockanchor 97",11=>"\\shplid 97",12=>"\\shpleft 97",13=>"\\shpgrp 97",
14=>"\\shpfhdr 97",15=>"\\shptxt 97",16=>"\\shpbypara 97",17=>"\\shptop 97",18=>"\\shpbymargin 97",19=>"\\shpbyignore 2000",
20=>"\\shpbxpage 97",21=>"\\shpbxmargin 97",22=>"\\shpbxignore 2000",23=>"\\shpbxcolumn 97",24=>"\\shprslt 97",25=>"\\shpfblwtxt 97",
26=>"\\shp",27=>"\\shpinst",28=>"\\sp",29=>"\\sn",30=>"\\sv"),
"type"=>array("Value","Value","Value","Value","Value","Destination","Value","Value","Flag","Value","Flag","Value","Value","Value","Value",
"Value","Flag","Value","Flag","Flag","Flag","Flag","Flag","Flag","Value","Value","Destination","Destination","Destination","Value","Value"),
"meaning"=>array())
)
?>