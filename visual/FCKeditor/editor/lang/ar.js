﻿/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: ar.js
 * 	Arabic language file.
 * 
 * File Authors:
 * 		Abdul-Aziz Abdul-Kareem Al-Oraij (http://aziz.oraij.com)
 * 		Abu Faisal (http://www.24at24.com)
 */

var FCKLang =
{
// Language direction : "ltr" (left to right) or "rtl" (right to left).
Dir					: "rtl",

ToolbarCollapse		: "ضم شريط الأدوات",
ToolbarExpand		: "تمدد شريط الأدوات",

// Toolbar Items and Context Menu
Save				: "حفظ",
NewPage				: "صفحة جديدة",
Preview				: "معاينة الصفحة",
Cut					: "قص",
Copy				: "نسخ",
Paste				: "لصق",
PasteText			: "لصق كنص بسيط",
PasteWord			: "لصق من وورد",
Print				: "طباعة",
SelectAll			: "تحديد الكل",
RemoveFormat		: "إزالة التنسيقات",
InsertLinkLbl		: "رابط",
InsertLink			: "إدراج/تحرير رابط",
RemoveLink			: "إزالة رابط",
Anchor				: "إدراج/تحرير إشارة مرجعية",
InsertImageLbl		: "صورة",
InsertImage			: "إدراج/تحرير صورة",
InsertFlashLbl		: "فلاش",
InsertFlash			: "إدراج/تحرير فيلم فلاش",
InsertTableLbl		: "جدول",
InsertTable			: "إدراج/تحرير جدول",
InsertLineLbl		: "خط فاصل",
InsertLine			: "إدراج خط فاصل",
InsertSpecialCharLbl: "رموز",
InsertSpecialChar	: "إدراج  رموز..ِ",
InsertSmileyLbl		: "ابتسامات",
InsertSmiley		: "إدراج ابتسامات",
About				: "حول FCKeditor",
Bold				: "غامق",
Italic				: "مائل",
Underline			: "تسطير",
StrikeThrough		: "يتوسطه خط",
Subscript			: "منخفض",
Superscript			: "مرتفع",
LeftJustify			: "محاذاة إلى اليسار",
CenterJustify		: "توسيط",
RightJustify		: "محاذاة إلى اليمين",
BlockJustify		: "ضبط",
DecreaseIndent		: "إنقاص المسافة البادئة",
IncreaseIndent		: "زيادة المسافة البادئة",
Undo				: "تراجع",
Redo				: "إعادة",
NumberedListLbl		: "تعداد رقمي",
NumberedList		: "إدراج/إلغاء تعداد رقمي",
BulletedListLbl		: "تعداد نقطي",
BulletedList		: "إدراج/إلغاء تعداد نقطي",
ShowTableBorders	: "معاينة حدود الجداول",
ShowDetails			: "معاينة التفاصيل",
Style				: "نمط",
FontFormat			: "تنسيق",
Font				: "خط",
FontSize			: "حجم الخط",
TextColor			: "لون النص",
BGColor				: "لون الخلفية",
Source				: "شفرة المصدر",
Find				: "بحث",
Replace				: "إستبدال",
SpellCheck			: "تدقيق إملائي",
UniversalKeyboard	: "لوحة المفاتيح العالمية",
PageBreakLbl		: "فصل الصفحة",
PageBreak			: "إدخال صفحة جديدة",

Form			: "نموذج",
Checkbox		: "خانة إختيار",
RadioButton		: "زر خيار",
TextField		: "مربع نص",
Textarea		: "ناحية نص",
HiddenField		: "إدراج حقل خفي",
Button			: "زر ضغط",
SelectionField	: "قائمة منسدلة",
ImageButton		: "زر صورة",

FitWindow		: "Maximize the editor size",	//MISSING

// Context Menu
EditLink			: "تحرير رابط",
CellCM				: "Cell",	//MISSING
RowCM				: "Row",	//MISSING
ColumnCM			: "Column",	//MISSING
InsertRow			: "إدراج صف",
DeleteRows			: "حذف صفوف",
InsertColumn		: "إدراج عمود",
DeleteColumns		: "حذف أعمدة",
InsertCell			: "إدراج خلية",
DeleteCells			: "حذف خلايا",
MergeCells			: "دمج خلايا",
SplitCell			: "تقسيم خلية",
TableDelete			: "حذف الجدول",
CellProperties		: "خصائص الخلية",
TableProperties		: "خصائص الجدول",
ImageProperties		: "خصائص الصورة",
FlashProperties		: "خصائص فيلم الفلاش",

AnchorProp			: "خصائص الإشارة المرجعية",
ButtonProp			: "خصائص زر الضغط",
CheckboxProp		: "خصائص خانة الإختيار",
HiddenFieldProp		: "خصائص الحقل الخفي",
RadioButtonProp		: "خصائص زر الخيار",
ImageButtonProp		: "خصائص زر الصورة",
TextFieldProp		: "خصائص مربع النص",
SelectionFieldProp	: "خصائص القائمة المنسدلة",
TextareaProp		: "خصائص ناحية النص",
FormProp			: "خصائص النموذج",

FontFormats			: "عادي;منسّق;دوس;العنوان 1;العنوان  2;العنوان  3;العنوان  4;العنوان  5;العنوان  6",

// Alerts and Messages
ProcessingXHTML		: "إنتظر قليلاً ريثما تتم   معالَجة‏ XHTML. لن يستغرق طويلاً...",
Done				: "تم",
PasteWordConfirm	: "يبدو أن النص المراد لصقه منسوخ من برنامج وورد. هل تود تنظيفه قبل الشروع في عملية اللصق؟",
NotCompatiblePaste	: "هذه الميزة تحتاج لمتصفح من النوعInternet Explorer إصدار 5.5 فما فوق. هل تود اللصق دون تنظيف الكود؟",
UnknownToolbarItem	: "عنصر شريط أدوات غير معروف \"%1\"",
UnknownCommand		: "أمر غير معروف \"%1\"",
NotImplemented		: "لم يتم دعم هذا الأمر",
UnknownToolbarSet	: "لم أتمكن من العثور على طقم الأدوات \"%1\" ",
NoActiveX			: "لتأمين متصفحك يجب أن تحدد بعض مميزات المحرر. يتوجب عليك تمكين الخيار \"Run ActiveX controls and plug-ins\". قد تواجة أخطاء وتلاحظ مميزات مفقودة",
BrowseServerBlocked : "لايمكن فتح مصدر المتصفح. فضلا يجب التأكد بأن جميع موانع النوافذ المنبثقة معطلة",
DialogBlocked		: "لايمكن فتح نافذة الحوار . فضلا تأكد من أن  مانع النوافذ المنبثة معطل .",

// Dialogs
DlgBtnOK			: "موافق",
DlgBtnCancel		: "إلغاء الأمر",
DlgBtnClose			: "إغلاق",
DlgBtnBrowseServer	: "تصفح الخادم",
DlgAdvancedTag		: "متقدم",
DlgOpOther			: "&lt;أخرى&gt;",
DlgInfoTab			: "معلومات",
DlgAlertUrl			: "الرجاء كتابة عنوان الإنترنت",

// General Dialogs Labels
DlgGenNotSet		: "&lt;بدون تحديد&gt;",
DlgGenId			: "الرقم",
DlgGenLangDir		: "إتجاه النص",
DlgGenLangDirLtr	: "اليسار لليمين (LTR)",
DlgGenLangDirRtl	: "اليمين لليسار (RTL)",
DlgGenLangCode		: "رمز اللغة",
DlgGenAccessKey		: "مفاتيح الإختصار",
DlgGenName			: "الاسم",
DlgGenTabIndex		: "الترتيب",
DlgGenLongDescr		: "عنوان الوصف المفصّل",
DlgGenClass			: "فئات التنسيق",
DlgGenTitle			: "تلميح الشاشة",
DlgGenContType		: "نوع التلميح",
DlgGenLinkCharset	: "ترميز المادة المطلوبة",
DlgGenStyle			: "نمط",

// Image Dialog
DlgImgTitle			: "خصائص الصورة",
DlgImgInfoTab		: "معلومات الصورة",
DlgImgBtnUpload		: "أرسلها للخادم",
DlgImgURL			: "موقع الصورة",
DlgImgUpload		: "رفع",
DlgImgAlt			: "الوصف",
DlgImgWidth			: "العرض",
DlgImgHeight		: "الإرتفاع",
DlgImgLockRatio		: "تناسق الحجم",
DlgBtnResetSize		: "إستعادة الحجم الأصلي",
DlgImgBorder		: "سمك الحدود",
DlgImgHSpace		: "تباعد أفقي",
DlgImgVSpace		: "تباعد عمودي",
DlgImgAlign			: "محاذاة",
DlgImgAlignLeft		: "يسار",
DlgImgAlignAbsBottom: "أسفل النص",
DlgImgAlignAbsMiddle: "وسط السطر",
DlgImgAlignBaseline	: "على السطر",
DlgImgAlignBottom	: "أسفل",
DlgImgAlignMiddle	: "وسط",
DlgImgAlignRight	: "يمين",
DlgImgAlignTextTop	: "أعلى النص",
DlgImgAlignTop		: "أعلى",
DlgImgPreview		: "معاينة",
DlgImgAlertUrl		: "فضلاً أكتب الموقع الذي توجد عليه هذه الصورة.",
DlgImgLinkTab		: "الرابط",

// Flash Dialog
DlgFlashTitle		: "خصائص فيلم الفلاش",
DlgFlashChkPlay		: "تشغيل تلقائي",
DlgFlashChkLoop		: "تكرار",
DlgFlashChkMenu		: "تمكين قائمة فيلم الفلاش",
DlgFlashScale		: "الحجم",
DlgFlashScaleAll	: "إظهار الكل",
DlgFlashScaleNoBorder	: "بلا حدود",
DlgFlashScaleFit	: "ضبط تام",

// Link Dialog
DlgLnkWindowTitle	: "إرتباط تشعبي",
DlgLnkInfoTab		: "معلومات الرابط",
DlgLnkTargetTab		: "الهدف",

DlgLnkType			: "نوع الربط",
DlgLnkTypeURL		: "العنوان",
DlgLnkTypeAnchor	: "مكان في هذا المستند",
DlgLnkTypeEMail		: "بريد إلكتروني",
DlgLnkProto			: "البروتوكول",
DlgLnkProtoOther	: "&lt;أخرى&gt;",
DlgLnkURL			: "الموقع",
DlgLnkAnchorSel		: "اختر علامة مرجعية",
DlgLnkAnchorByName	: "حسب اسم العلامة",
DlgLnkAnchorById	: "حسب تعريف العنصر",
DlgLnkNoAnchors		: "&lt;لا يوجد علامات مرجعية في هذا المستند&gt;",
DlgLnkEMail			: "عنوان بريد إلكتروني",
DlgLnkEMailSubject	: "موضوع الرسالة",
DlgLnkEMailBody		: "محتوى الرسالة",
DlgLnkUpload		: "رفع",
DlgLnkBtnUpload		: "أرسلها للخادم",

DlgLnkTarget		: "الهدف",
DlgLnkTargetFrame	: "&lt;إطار&gt;",
DlgLnkTargetPopup	: "&lt;نافذة منبثقة&gt;",
DlgLnkTargetBlank	: "إطار جديد (_blank)",
DlgLnkTargetParent	: "الإطار الأصل (_parent)",
DlgLnkTargetSelf	: "نفس الإطار (_self)",
DlgLnkTargetTop		: "صفحة كاملة (_top)",
DlgLnkTargetFrameName	: "اسم الإطار الهدف",
DlgLnkPopWinName	: "تسمية النافذة المنبثقة",
DlgLnkPopWinFeat	: "خصائص النافذة المنبثقة",
DlgLnkPopResize		: "قابلة للتحجيم",
DlgLnkPopLocation	: "شريط العنوان",
DlgLnkPopMenu		: "القوائم الرئيسية",
DlgLnkPopScroll		: "أشرطة التمرير",
DlgLnkPopStatus		: "شريط الحالة السفلي",
DlgLnkPopToolbar	: "شريط الأدوات",
DlgLnkPopFullScrn	: "ملئ الشاشة (IE)",
DlgLnkPopDependent	: "تابع (Netscape)",
DlgLnkPopWidth		: "العرض",
DlgLnkPopHeight		: "الإرتفاع",
DlgLnkPopLeft		: "التمركز لليسار",
DlgLnkPopTop		: "التمركز للأعلى",

DlnLnkMsgNoUrl		: "فضلاً أدخل عنوان الموقع الذي يشير إليه الرابط",
DlnLnkMsgNoEMail	: "فضلاً أدخل عنوان البريد الإلكتروني",
DlnLnkMsgNoAnchor	: "فضلاً حدد العلامة المرجعية المرغوبة",

// Color Dialog
DlgColorTitle		: "اختر لوناً",
DlgColorBtnClear	: "مسح",
DlgColorHighlight	: "تحديد",
DlgColorSelected	: "إختيار",

// Smiley Dialog
DlgSmileyTitle		: "إدراج إبتسامات ",

// Special Character Dialog
DlgSpecialCharTitle	: "إدراج رمز",

// Table Dialog
DlgTableTitle		: "إدراج جدول",
DlgTableRows		: "صفوف",
DlgTableColumns		: "أعمدة",
DlgTableBorder		: "سمك الحدود",
DlgTableAlign		: "المحاذاة",
DlgTableAlignNotSet	: "<بدون تحديد>",
DlgTableAlignLeft	: "يسار",
DlgTableAlignCenter	: "وسط",
DlgTableAlignRight	: "يمين",
DlgTableWidth		: "العرض",
DlgTableWidthPx		: "بكسل",
DlgTableWidthPc		: "بالمئة",
DlgTableHeight		: "الإرتفاع",
DlgTableCellSpace	: "تباعد الخلايا",
DlgTableCellPad		: "المسافة البادئة",
DlgTableCaption		: "الوصف",
DlgTableSummary		: "الخلاصة",

// Table Cell Dialog
DlgCellTitle		: "خصائص الخلية",
DlgCellWidth		: "العرض",
DlgCellWidthPx		: "بكسل",
DlgCellWidthPc		: "بالمئة",
DlgCellHeight		: "الإرتفاع",
DlgCellWordWrap		: "التفاف النص",
DlgCellWordWrapNotSet	: "<بدون تحديد>",
DlgCellWordWrapYes	: "نعم",
DlgCellWordWrapNo	: "لا",
DlgCellHorAlign		: "المحاذاة الأفقية",
DlgCellHorAlignNotSet	: "<بدون تحديد>",
DlgCellHorAlignLeft	: "يسار",
DlgCellHorAlignCenter	: "وسط",
DlgCellHorAlignRight: "يمين",
DlgCellVerAlign		: "المحاذاة العمودية",
DlgCellVerAlignNotSet	: "<بدون تحديد>",
DlgCellVerAlignTop	: "أعلى",
DlgCellVerAlignMiddle	: "وسط",
DlgCellVerAlignBottom	: "أسفل",
DlgCellVerAlignBaseline	: "على السطر",
DlgCellRowSpan		: "إمتداد الصفوف",
DlgCellCollSpan		: "إمتداد الأعمدة",
DlgCellBackColor	: "لون الخلفية",
DlgCellBorderColor	: "لون الحدود",
DlgCellBtnSelect	: "حدّد...",

// Find Dialog
DlgFindTitle		: "بحث",
DlgFindFindBtn		: "ابحث",
DlgFindNotFoundMsg	: "لم يتم العثور على النص المحدد.",

// Replace Dialog
DlgReplaceTitle			: "إستبدال",
DlgReplaceFindLbl		: "البحث عن:",
DlgReplaceReplaceLbl	: "إستبدال بـ:",
DlgReplaceCaseChk		: "مطابقة حالة الأحرف",
DlgReplaceReplaceBtn	: "إستبدال",
DlgReplaceReplAllBtn	: "إستبدال الكل",
DlgReplaceWordChk		: "الكلمة بالكامل فقط",

// Paste Operations / Dialog
PasteErrorPaste	: "الإعدادات الأمنية للمتصفح الذي تستخدمه تمنع اللصق التلقائي. فضلاً إستخدم لوحة المفاتيح لفعل ذلك (Ctrl+V).",
PasteErrorCut	: "الإعدادات الأمنية للمتصفح الذي تستخدمه تمنع القص التلقائي. فضلاً إستخدم لوحة المفاتيح لفعل ذلك (Ctrl+X).",
PasteErrorCopy	: "الإعدادات الأمنية للمتصفح الذي تستخدمه تمنع النسخ التلقائي. فضلاً إستخدم لوحة المفاتيح لفعل ذلك (Ctrl+C).",

PasteAsText		: "لصق كنص بسيط",
PasteFromWord	: "لصق من وورد",

DlgPasteMsg2	: "الصق داخل الصندوق بإستخدام زرّي (<STRONG>Ctrl+V</STRONG>) في لوحة المفاتيح، ثم اضغط زر  <STRONG>موافق</STRONG>.",
DlgPasteIgnoreFont		: "تجاهل تعريفات أسماء الخطوط",
DlgPasteRemoveStyles	: "إزالة تعريفات الأنماط",
DlgPasteCleanBox		: "نظّف محتوى الصندوق",

// Color Picker
ColorAutomatic	: "تلقائي",
ColorMoreColors	: "ألوان إضافية...",

// Document Properties
DocProps		: "خصائص الصفحة",

// Anchor Dialog
DlgAnchorTitle		: "خصائص إشارة مرجعية",
DlgAnchorName		: "اسم الإشارة المرجعية",
DlgAnchorErrorName	: "الرجاء كتابة اسم الإشارة المرجعية",

// Speller Pages Dialog
DlgSpellNotInDic		: "ليست في القاموس",
DlgSpellChangeTo		: "التغيير إلى",
DlgSpellBtnIgnore		: "تجاهل",
DlgSpellBtnIgnoreAll	: "تجاهل الكل",
DlgSpellBtnReplace		: "تغيير",
DlgSpellBtnReplaceAll	: "تغيير الكل",
DlgSpellBtnUndo			: "تراجع",
DlgSpellNoSuggestions	: "- لا توجد إقتراحات -",
DlgSpellProgress		: "جاري التدقيق إملائياً",
DlgSpellNoMispell		: "تم إكمال التدقيق الإملائي: لم يتم العثور على أي أخطاء إملائية",
DlgSpellNoChanges		: "تم إكمال التدقيق الإملائي: لم يتم تغيير أي كلمة",
DlgSpellOneChange		: "تم إكمال التدقيق الإملائي: تم تغيير كلمة واحدة فقط",
DlgSpellManyChanges		: "تم إكمال التدقيق الإملائي: تم تغيير %1 كلمات\كلمة",

IeSpellDownload			: "المدقق الإملائي (الإنجليزي) غير مثبّت. هل تود تحميله الآن؟",

// Button Dialog
DlgButtonText	: "القيمة/التسمية",
DlgButtonType	: "نوع الزر",

// Checkbox and Radio Button Dialogs
DlgCheckboxName		: "الاسم",
DlgCheckboxValue	: "القيمة",
DlgCheckboxSelected	: "محدد",

// Form Dialog
DlgFormName		: "الاسم",
DlgFormAction	: "اسم الملف",
DlgFormMethod	: "الأسلوب",

// Select Field Dialog
DlgSelectName		: "الاسم",
DlgSelectValue		: "القيمة",
DlgSelectSize		: "الحجم",
DlgSelectLines		: "الأسطر",
DlgSelectChkMulti	: "السماح بتحديدات متعددة",
DlgSelectOpAvail	: "الخيارات المتاحة",
DlgSelectOpText		: "النص",
DlgSelectOpValue	: "القيمة",
DlgSelectBtnAdd		: "إضافة",
DlgSelectBtnModify	: "تعديل",
DlgSelectBtnUp		: "تحريك لأعلى",
DlgSelectBtnDown	: "تحريك لأسفل",
DlgSelectBtnSetValue : "إجعلها محددة",
DlgSelectBtnDelete	: "إزالة",

// Textarea Dialog
DlgTextareaName	: "الاسم",
DlgTextareaCols	: "الأعمدة",
DlgTextareaRows	: "الصفوف",

// Text Field Dialog
DlgTextName			: "الاسم",
DlgTextValue		: "القيمة",
DlgTextCharWidth	: "العرض بالأحرف",
DlgTextMaxChars		: "عدد الحروف الأقصى",
DlgTextType			: "نوع المحتوى",
DlgTextTypeText		: "نص",
DlgTextTypePass		: "كلمة مرور",

// Hidden Field Dialog
DlgHiddenName	: "الاسم",
DlgHiddenValue	: "القيمة",

// Bulleted List Dialog
BulletedListProp	: "خصائص التعداد النقطي",
NumberedListProp	: "خصائص التعداد الرقمي",
DlgLstType			: "النوع",
DlgLstTypeCircle	: "دائرة",
DlgLstTypeDisc		: "قرص",
DlgLstTypeSquare	: "مربع",
DlgLstTypeNumbers	: "أرقام (1، 2، 3)َ",
DlgLstTypeLCase		: "حروف صغيرة (a, b, c)َ",
DlgLstTypeUCase		: "حروف كبيرة (A, B, C)َ",
DlgLstTypeSRoman	: "ترقيم روماني صغير (i, ii, iii)َ",
DlgLstTypeLRoman	: "ترقيم روماني كبير (I, II, III)َ",

// Document Properties Dialog
DlgDocGeneralTab	: "عام",
DlgDocBackTab		: "الخلفية",
DlgDocColorsTab		: "الألوان والهوامش",
DlgDocMetaTab		: "المعرّفات الرأسية",

DlgDocPageTitle		: "عنوان الصفحة",
DlgDocLangDir		: "إتجاه اللغة",
DlgDocLangDirLTR	: "اليسار لليمين (LTR)",
DlgDocLangDirRTL	: "اليمين لليسار (RTL)",
DlgDocLangCode		: "رمز اللغة",
DlgDocCharSet		: "ترميز الحروف",
DlgDocCharSetOther	: "ترميز حروف آخر",

DlgDocDocType		: "ترويسة نوع  الصفحة",
DlgDocDocTypeOther	: "ترويسة نوع  صفحة أخرى",
DlgDocIncXHTML		: "تضمين   إعلانات‏ لغة XHTMLَ",
DlgDocBgColor		: "لون الخلفية",
DlgDocBgImage		: "رابط الصورة الخلفية",
DlgDocBgNoScroll	: "جعلها علامة مائية",
DlgDocCText			: "النص",
DlgDocCLink			: "الروابط",
DlgDocCVisited		: "المزارة",
DlgDocCActive		: "النشطة",
DlgDocMargins		: "هوامش الصفحة",
DlgDocMaTop			: "علوي",
DlgDocMaLeft		: "أيسر",
DlgDocMaRight		: "أيمن",
DlgDocMaBottom		: "سفلي",
DlgDocMeIndex		: "الكلمات الأساسية (مفصولة بفواصل)َ",
DlgDocMeDescr		: "وصف الصفحة",
DlgDocMeAuthor		: "الكاتب",
DlgDocMeCopy		: "المالك",
DlgDocPreview		: "معاينة",

// Templates Dialog
Templates			: "القوالب",
DlgTemplatesTitle	: "قوالب المحتوى",
DlgTemplatesSelMsg	: "اختر القالب الذي تود وضعه في المحرر <br>(سيتم فقدان المحتوى الحالي):",
DlgTemplatesLoading	: "جاري تحميل قائمة القوالب، الرجاء الإنتظار...",
DlgTemplatesNoTpl	: "(لم يتم تعريف أي قالب)",

// About Dialog
DlgAboutAboutTab	: "نبذة",
DlgAboutBrowserInfoTab	: "معلومات متصفحك",
DlgAboutLicenseTab	: "License",	//MISSING
DlgAboutVersion		: "الإصدار",
DlgAboutLicense		: "مرخّص بحسب قانون  GNU LGPL",
DlgAboutInfo		: "لمزيد من المعلومات تفضل بزيارة"
}