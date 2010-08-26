; max_install.nsi
;
; Athletica_WinMAX
; ----------------
;
; NSIS-Script used to build the Athletica_WinMAX-Package. If you
; have NSIS installed on your system, you may rebuild the package
; by compiling this script from this installation directory.
;
;

;--------------------------------
;
; Basic variables
;

!define VERSION "4.0"
!define OS "?"

;
;--------------------------------

;--------------------------------
;
; Strings 
;


LoadLanguageFile "${NSISDIR}\Contrib\Language files\German.nlf"
LoadLanguageFile "${NSISDIR}\Contrib\Language files\English.nlf"
LoadLanguageFile "${NSISDIR}\Contrib\Language files\French.nlf"
LoadLanguageFile "${NSISDIR}\Contrib\Language files\Italian.nlf"

LangString langDlgTitle ${LANG_GERMAN} "Installationssprache"
LangString langDlgTitle ${LANG_ENGLISH} "Installation language"
LangString langDlgTitle ${LANG_FRENCH} "Langue d’installation"
LangString langDlgTitle ${LANG_ITALIAN} "Lingua d’installazione"
LangString langDlgText  ${LANG_GERMAN} "Bitte Installationssprache wählen"
LangString langDlgText ${LANG_ENGLISH} "Please choose installation language"
LangString langDlgText ${LANG_FRENCH} "Veuillez sélectionner une langue d’installation"
LangString langDlgText ${LANG_ITALIAN} "Prego, scegliera la lingua d’installazione"

LangString iCaption ${LANG_GERMAN} "Athletica ${VERSION} installieren"
LangString iCaption ${LANG_ENGLISH} "Install Athletica ${VERSION}"
LangString iCaption ${LANG_FRENCH} "Installer Athletica ${VERSION}"
LangString iCaption ${LANG_ITALIAN} "Installazione d’Athletica ${VERSION}"

LangString licText ${LANG_GERMAN} "Athletica Lizenz: GNU General Public License"
LangString licText ${LANG_ENGLISH} "Athletica license: GNU General Public License"
LangString licText ${LANG_FRENCH} "Licence Athletica: GNU General Public License"
LangString licText ${LANG_ITALIAN} "Licenza d’Athletica: GNU General Public License"

LangString apacheInstalled ${LANG_GERMAN} "Ein Apache Webserver ist bereits installiert.$\nBitte die ZIP-Version von Athletica herunterladen und Profi-Installation durchführen oder Apache zuerst deinstallieren"
LangString apacheInstalled ${LANG_ENGLISH} "An Apache web server is already installed on your system.$\nPlease download the ZIP-version of Athletica and follow the instructions for professional installations or uninstall Apache first."
LangString apacheInstalled ${LANG_FRENCH} "Un serveur web Apache est déjà installé. Veuillez télécharger la version ZIP et exécuter l’installation Profi ou d’abord désinstaller Apache."
LangString apacheInstalled ${LANG_ITALIAN} "E’ giä installato un server Apache. Siete pregati di scaricare una versione ZIP di Atletica oppure eseguire una installazione professionale oppure disinstallare prima Apache."

LangString athleticaInstalled ${LANG_GERMAN} "Sie versuchen, Athletica ${VERSION} zu installieren. Bitte deinstallieren Sie zuerst die bereits vorhandene Version $R1."
LangString athleticaInstalled ${LANG_ENGLISH} "You are trying to install Athletica ${VERSION}. Please first deinstall the current version $R1."
LangString athleticaInstalled ${LANG_FRENCH} "Vous essayez d’installer Athletica ${VERSION}. Veuillez d’abord désinstaller la version $R1 existante."
LangString athleticaInstalled ${LANG_ITALIAN} "State cercano di installare una nuova versione di Athletica. Disinstallate dapprima la versione $R1 in uso."

LangString dText ${LANG_GERMAN} "Installations-Verzeichnis für Athletica Komponenten"
LangString dText ${LANG_ENGLISH} "Installation directory for Athletica components"
LangString dText ${LANG_FRENCH} "Liste d’installation pour les composantes Athletica"
LangString dText ${LANG_ITALIAN} "Indice die componenti d’installazione di Athletica"

LangString dSubText ${LANG_GERMAN} "Alle Athletica Komponenten werden in dieses Verzeichnis installiert:"
LangString dSubText ${LANG_ENGLISH} "All Athletica components will be installed in this directory:"
LangString dSubText ${LANG_FRENCH} "Toutes les composantes Athletica sont installées dans cette liste:"
LangString dSubText ${LANG_ITALIAN} "Tutti i componenti di Athletica verranno installati sotto questo indice:"

LangString compText ${LANG_GERMAN} "Athletica Komponenten"
LangString compText ${LANG_ENGLISH} "Athletica components"
LangString compText ${LANG_FRENCH} "Composantes Athletica"
LangString compText ${LANG_ITALIAN} "Componenti d’Athletica"

LangString basicdata ${LANG_GERMAN} "Basisdaten (Kategorien, Disziplinen)"
LangString basicdata ${LANG_ENGLISH} "Basic data (categories, disciplines)"
LangString basicdata ${LANG_FRENCH} "Données de base (catégories, disciplines)"
LangString basicdata ${LANG_ITALIAN} "Dati di base (Categorie e discipline)"

LangString basics ${LANG_GERMAN} "basics_de.sql"
LangString basics ${LANG_ENGLISH} "basics_de.sql"
LangString basics ${LANG_FRENCH} "basics_de.sql"
LangString basics ${LANG_ITALIAN} "basics_de.sql"

LangString club_ch ${LANG_GERMAN} "Vereine (Schweiz)"
LangString club_ch ${LANG_ENGLISH} "Clubs (Switzerland)"
LangString club_ch ${LANG_FRENCH} "Sociétés (Suisse)"
LangString club_ch ${LANG_ITALIAN} "Societä (Svizzere)"

LangString basedata ${LANG_GERMAN} "Stammdaten SLV"
LangString basedata ${LANG_ENGLISH} "Basedata SLV"
LangString basedata ${LANG_FRENCH} "Données de base FSA"
LangString basedata ${LANG_ITALIAN} "Banca dati FSAL"

LangString invalidOS ${LANG_GERMAN} "Betriebssystem nicht unterstützt: Windows "
LangString invalidOS ${LANG_ENGLISH} "Operating system not supportet: Windows "
LangString invalidOS ${LANG_FRENCH} "Système d’exploitation non soutenu: Windows "
LangString invalidOS ${LANG_ITALIAN} "Il sistema operativo non é supportato da Windows "

LangString missingDLL ${LANG_GERMAN} "Ein wichtiges System-File fehlt, um Athletica automatisch zu installieren.$\nBitte die ZIP-Version von Athletica herunterladen und die Profi-Installation durchführen oder Internet Explorer Version 4.x oder grösser installieren."
LangString missingDLL ${LANG_ENGLISH} "An important system file is missing to install Athletica.$\nPlease download the ZIP-version and follow the instructions for professional installations or install Internet Explorer version 4.x or later."
LangString missingDLL ${LANG_FRENCH} "Un fichier important du système manque pour l’installation automatique d’Athletica. Veuillez télécharger la version ZIP d’Athletica et exécuter l’installation Profi ou installer  Internet Explorer Version 4.x ou plus grande."
LangString missingDLL ${LANG_ITALIAN} "Per installare Athletica manca un importante file di sistema. Siete pregati di installare una versione ZIP di Atletica e di procedere alla installazione di tipo professionale oppure installare Internet Explorer versione 4.x o o maggiore."

LangString mysqlInstalled ${LANG_GERMAN} "Ein MySQL Datenbank-Server ist bereits installiert.$\nBitte die ZIP-Version von Athletica herunterladen und Profi-Installation durchführen oder MySQL zuerst deinstallieren."
LangString mysqlInstalled ${LANG_ENGLISH} "A MySQL database engine is already installed on your system.$\nPlease download the ZIP-version of Athletica and follow the instructions for professional installations or uninstall MySQL first."
LangString mysqlInstalled ${LANG_FRENCH} "Un serveur de banque des données MySQL est déjà installé. Veuillez télécharger la version ZIP d’Athletica et exécuter l’installation Profi ou d’abord désinstaller MySQL."
LangString mysqlInstalled ${LANG_ITALIAN} "Una banca data del tipo MySQL é già installato. Siete pregati di installare una versione ZIP di Athletica e di procedere alla disinstallazione di MySQL."

LangString notAdmin ${LANG_GERMAN} "Benützer ist nicht Administrator auf diesem System."
LangString notAdmin ${LANG_ENGLISH} "User does not have administrator privileges on this system."
LangString notAdmin ${LANG_FRENCH} "L’utilisateur n’est pas administrateur sur ce système."
LangString notAdmin ${LANG_ITALIAN} "L’utilizzatore non é amministratore di sistema."

LangString un.notAdmin ${LANG_GERMAN} "Benützer ist nicht Administrator auf diesem System."
LangString un.notAdmin ${LANG_ENGLISH} "User does not have administrator privileges on this system."
LangString un.notAdmin ${LANG_FRENCH} "L’utilisateur n’est pas administrateur sur ce système."
LangString un.notAdmin ${LANG_ITALIAN} "L’utilizzatore non é amministratore di sistema."

LangString readme ${LANG_GERMAN} "$INSTDIR\readme_de.txt"
LangString readme ${LANG_ENGLISH} "$INSTDIR\readme_en.txt"
LangString readme ${LANG_FRENCH} "$INSTDIR\readme_en.txt"
LangString readme ${LANG_ITALIAN} "$INSTDIR\readme_en.txt"

LangString type1 ${LANG_GERMAN} "Standard"
LangString type1 ${LANG_ENGLISH} "Standard"
LangString type1 ${LANG_FRENCH} "Standard"
LangString type1 ${LANG_ITALIAN} "Standard"

LangString type2 ${LANG_GERMAN} "Minimal"
LangString type2 ${LANG_ENGLISH} "Minimal"
LangString type2 ${LANG_FRENCH} "Minimal"
LangString type2 ${LANG_ITALIAN} "Minimo"

LangString msg ${LANG_GERMAN} "Die Installation ist abgeschlossen.$\n$\nSoll Athletica jetzt gestartet werden?"
LangString msg ${LANG_ENGLISH} "Installation is completed.$\n$\nDo you want to start Athletica now?"
LangString msg ${LANG_FRENCH} "L’installation est terminée.$\n$\nVoulez-vous démarrer Athletica maintenant?"
LangString msg ${LANG_ITALIAN} "Linstallazione ö terminata.$\n$\nAthletica va riavviato ora?"

LangString un.msg ${LANG_GERMAN} "Achtung: $INSTDIR konnte nicht gelöscht werden!"
LangString un.msg ${LANG_ENGLISH} "Note: $INSTDIR could not be removed!"
LangString un.msg ${LANG_FRENCH} "Attention: $INSTDIR n’a pas pu être effacé!"
LangString un.msg ${LANG_ITALIAN} "Attenzione: $INSTDIR non è stato eliminato!"

LangString un.iCaption ${LANG_GERMAN} "Athletica ${VERSION} deinstallieren"
LangString un.iCaption ${LANG_ENGLISH} "Uninstall Athletica ${VERSION}"
LangString un.iCaption ${LANG_FRENCH} "Désinstaller Athletica ${VERSION}"
LangString un.iCaption ${LANG_ITALIAN} "Disintallazione di Athletica ${VERSION}"

LangString un.iText ${LANG_GERMAN} "Athletica und sämtliche Zusatzkomponenten (MySQL, Apache, PHP) werden deinstalliert.$\n$\nWollen Sie fortfahren?"
LangString un.iText ${LANG_ENGLISH} "Athletica and all added components (MySQL, Apache, PHP) will be uninstalled.$\n$\nDo you want to continue?"
LangString un.iText ${LANG_FRENCH} "Athletica et toutes les autres composantes complémentaires (MySQL, Apache, PHP) sont désinstallées.$\n$\nVoulez-vous continuer?"
LangString un.iText ${LANG_ITALIAN} "Athletica e tutte le sue componenti(MySQL, Apache, PHP) verranno disinstallati.$\n$\nVolete procedere?"

;
; -----------------------------------


;--------------------------------
;
; General options
;

Name "Athletica"

BrandingText "http://code.google.com/p/athletica"

Caption "$(iCaption)"

ComponentText "$(compText)"

DirText "$(dText)" "$(dSubText)"

InstallDir $PROGRAMFILES\athletica

InstProgressFlags smooth
InstallColors /windows

InstType $(type1)
InstType $(type2)

LicenseData "license.txt"
LicenseText "$(licText)"

OutFile "Athletica_WinMAX_${VERSION}.exe"

ShowInstDetails show
ShowUninstDetails show

UninstallCaption "$(un.iCaption)"

UninstallText "$(un.iText)"

;
; -----------------------------------


;--------------------------------
;
;	Pages
;
Page license
Page components
Page directory	
Page instfiles
Page custom postInfo
;
; -----------------------------------


;--------------------------------
;
;	Athletica installation
;

Section "!Athletica"
	AddSize 0
	SectionIn RO
	
	; create installation directory and copy all files
	CreateDirectory $INSTDIR
	SetOutPath $INSTDIR
	File /r *.*
	
	; update configuration files to reflect current installation path
	; (use forward slashes in path)
	FileOpen $R0 "temp\path.txt" w
	FileWrite $R0 "$INSTDIR"
	FileClose $R0
	ExecWait "temp\sed\sed -i s/\\/\\\//g temp\path.txt"	
	
	FileOpen $R0 "temp\path.txt" r
	FileOpen $R1 "temp\sedscript.txt" w
	FileRead $R0 $R2
	FileWrite $R1 "s/\[ATHLETICA\]/$R2/"
	FileClose $R0
	FileClose $R1
	ExecWait "temp\sed\sed -i -f temp\sedscript.txt apache\conf\httpd.conf"
	ExecWait "temp\sed\sed -i -f temp\sedscript.txt my.ini"
	
	Delete "temp\path.txt"
	Delete "temp\sedscript.txt"
	
	; (use backward slashes in path)
	FileOpen $R0 "temp\path.txt" w
	FileWrite $R0 "$INSTDIR"
	FileClose $R0
	ExecWait "temp\sed\sed -i s/\\/\\\\/g temp\path.txt"
	
	FileOpen $R0 "temp\path.txt" r
	FileOpen $R1 "temp\sedscript.txt" w
	FileRead $R0 $R2
	FileWrite $R1 "s/\[ATHLETICA\]/$R2/"
	FileClose $R0
	FileClose $R1
	ExecWait "temp\sed\sed -i -f temp\sedscript.txt php\php.ini"
	
	Delete "temp\path.txt"
	Delete "temp\sedscript.txt"
	
	; copy INI-file to windows system directory
	CopyFiles my.ini $WINDIR
	
	; Athletica shortcuts
	SetShellVarContext all
  	CreateDirectory "$SMPROGRAMS\Athletica"       
    
    CreateShortCut "$DESKTOP\Athletica.lnk" "$INSTDIR\Athletica.url" "" "$INSTDIR\www\athletica\img\athletica.ico" 0 SW_SHOWMAXIMIZED "" "Athletica"
    CreateShortCut "$SMPROGRAMS\Athletica\Athletica.lnk" "$INSTDIR\Athletica.url" "" "$INSTDIR\www\athletica\img\athletica.ico" 0
  	
	CreateShortCut "$SMPROGRAMS\Athletica\Uninstall.lnk" "$INSTDIR\Uninstall.exe"

	; Database shortcuts
  	;CreateShortCut "$DESKTOP\phpMyAdmin.lnk" "$INSTDIR\phpMyAdmin.url"
	CreateDirectory "$SMPROGRAMS\Athletica\MySQL"
  	CreateShortCut "$SMPROGRAMS\Athletica\MySQL\phpMyAdmin.lnk" "$INSTDIR\phpMyAdmin.url"
  	CreateShortCut "$SMPROGRAMS\Athletica\MySQL\winmysqladmin.lnk" "$INSTDIR\mysql\bin\winmysqladmin.exe"

	; Apache shortcuts
	CreateDirectory "$SMPROGRAMS\Athletica\Apache"
  	CreateShortCut "$SMPROGRAMS\Athletica\Apache\httpd.conf.lnk" "$INSTDIR\apache\conf\httpd.conf"
  	CreateShortCut "$SMPROGRAMS\Athletica\Apache\Error log.lnk" "$INSTDIR\apache\logs\error.log"

	; Uninstaller
	WriteUninstaller $INSTDIR\uninstall.exe

	; Registry: Application settings
 	WriteRegStr HKLM "SOFTWARE\Athletica\Athletica" "VERSION" "${VERSION}"
 	WriteRegStr HKLM "SOFTWARE\Athletica\Athletica" "PATH" "$INSTDIR"
 	WriteRegStr HKLM "SOFTWARE\Athletica\MySQL" "PATH" "$INSTDIR\mysql\bin"

	; Registry: Uninstall string
  	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Athletica" "DisplayName" "Athletica"
  	WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Athletica" "UninstallString" '"$INSTDIR\uninstall.exe"'

	; configure start up 
	Pop $R0
	StrCmp $R0 "98" Win98
	StrCmp $R0 "ME" Win98
	Goto WinNT
	Win98:
		; install MySQL 
		ExecWait "$INSTDIR\mysql\bin\mysqladmin -u root shutdown" ;stop it first
		Exec "$INSTDIR\mysql\bin\winmysqladmin.exe"
  		CreateShortCut "$SMSTARTUP\winmysqladmin.lnk" "$INSTDIR\mysql\bin\winmysqladmin.exe"
		; install Apache 
		Exec "$INSTDIR\apache\apache -k shutdown"		;stop it first
		Exec "$INSTDIR\apache\apache"
  		CreateShortCut "$SMSTARTUP\Start Apache.lnk" "$INSTDIR\apache\Apache.exe"
	  	CreateShortCut "$SMPROGRAMS\Athletica\Apache\Start Apache.lnk" "$INSTDIR\apache\Apache.exe"
  		CreateShortCut "$SMPROGRAMS\Athletica\Apache\Stop Apache.lnk" "$INSTDIR\apache\Apache.exe" "-k shutdown"
  		CreateShortCut "$SMPROGRAMS\Athletica\Apache\Restart Apache.lnk" "$INSTDIR\apache\Apache.exe" "-k restart"
		Return

	WinNT:
		; install MySQL as a service and start it
		ExecWait "$INSTDIR\mysql\bin\mysqld-nt --install"
		ExecWait "NET START MySQL"

		; install Apache as a service and start it
		ExecWait "$INSTDIR\apache\bin\apache -k install -n Apache"	; install new service
		ExecWait "NET START Apache"

	  	CreateShortCut "$SMPROGRAMS\Athletica\Apache\Start Apache.lnk" "$INSTDIR\apache_netstart.bat"
  		CreateShortCut "$SMPROGRAMS\Athletica\Apache\Stop Apache.lnk" "$INSTDIR\apache_netstop.bat"
  		CreateShortCut "$SMPROGRAMS\Athletica\Apache\Restart Apache.lnk" "$INSTDIR\apache_restart.bat"

	; Add Apache & MySQL to the Windows Firewall
	SimpleFC::EnableDisableApplication "$INSTDIR\apache\bin\Apache.exe" 1
	Pop $0 ; return error(1)/success(0)

	SimpleFC::EnableDisableApplication "$INSTDIR\mysql\bin\mysqld-nt.exe" 1
	Pop $0 ; return error(1)/success(0)

SectionEnd

;
; -----------------------------------


;--------------------------------
;
;	Data feed	
;

;Section $(basicdata)
;	AddSize 20
;	SectionIn 1 
;	StrCpy $0 $(basics) 
;	Call runSQL
;SectionEnd

;Section $(club_ch)
;	SectionIn 1 
;	AddSize 30
;	StrCpy $0 "clubs_ch.sql" 
;	Call runSQL
;SectionEnd

;Section $(basedata)
;	SectionIn 1 
;	AddSize 30
;	StrCpy $0 "stammdaten.sql" 
;	Call runSQL
;SectionEnd

;
; -----------------------------------


;--------------------------------
;
;	Uninstall everything 
;

Section "Uninstall"
 	ReadRegStr $R1 HKLM "SOFTWARE\Athletica\Athletica" "PATH"
	SetShellVarContext all
	; remove MySQL service
	Pop $R0
	StrCmp $R0 "98" Win98
	StrCmp $R0 "ME" Win98
	Goto WinNT
	Win98:
		; stop Apache
		ExecWait "$R1\apache\bin\apache -k shutdown"
  		Delete "$SMSTARTUP\winmysqladmin.lnk"
  		Delete "$SMSTARTUP\Start Apache.lnk"
		; stop MySQL 
		ExecWait "$R1\mysql\bin\mysqladmin -u root shutdown"
  		Delete "$SMSTARTUP\winmysqladmin.lnk"
		Goto next

	WinNT:
		; stop and remove Apache service
		ReadRegStr $R2 HKLM "SYSTEM\CurrentControlSet\Services\Apache" "ImagePath"
		StrCmp $R2 "" next1				; check if service path empty
			ExecWait "NET STOP Apache"			; stop apache (if any)
			ExecWait "$R1\apache\bin\apache -k uninstall -n Apache"	; remove service (if any)
			Goto next1
	next1:

		; stop and remove MySQL service
		ReadRegStr $R2 HKLM "SYSTEM\CurrentControlSet\Services\MySQL" "ImagePath"
		StrCmp $R2 "" next				; check if service path empty
			ExecWait "NET STOP MySQL"	; stop mysql service (if any)
			ExecWait "$R2 --remove"		; remove service (if any)
	next:

	; remove Athletica directory
	RMDir /r $R1
	Delete $WINDIR\my.ini

	; Registry strings
 	DeleteRegKey HKLM "SOFTWARE\Athletica"
  	DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Athletica"

	; Athletica shortcuts
	RMDir /r $SMPROGRAMS\Athletica
  	Delete $DESKTOP\Athletica.lnk
  	Delete $DESKTOP\phpMyAdmin.lnk

	IfFileExists "$R1" 0 NoErrorMsg
   	MessageBox MB_OK $(un.msg) IDOK 0
	NoErrorMsg:
SectionEnd



;--------------------------------
;
;	various functions
;

Function .onInit
	;Language selection dialog
	Push ""
	Push ${LANG_GERMAN}
	Push Deutsch
	Push ${LANG_FRENCH}
	Push Français
	Push ${LANG_ITALIAN}
	Push Italiano
	Push ${LANG_ENGLISH}
	Push English
	Push A ; A means auto count languages
	       ; for the auto count to work the first empty push (Push "") must remain
	LangDLL::LangDialog "$(langDlgTitle)" "$(langDlgText)"

	Pop $LANGUAGE
	StrCmp $LANGUAGE "cancel" 0 +2
		Abort
FunctionEnd


Function .onGUIInit
	; check OS Version
	Call GetWindowsVersion
	Pop $R0
	StrCpy $R1 $R0 3			; truncate OS Version to three chars.
	Push $R1
	StrCmp $R1 "95" error
	StrCmp $R1 "NT 3" error
	StrCmp $R1 "" error
	Goto ok_1
	error:
		MessageBox MB_ICONEXCLAMATION "$(invalidOS)${OS}"
		Abort
	ok_1:

	; check admin user
	StrCmp $R0 "98" ok_2
	StrCmp $R0 "ME" ok_2
		UserInfo::GetName
		IfErrors ok_2
		Pop $0
		UserInfo::GetAccountType
		Pop $1
		StrCmp $1 "Admin" ok_2
			MessageBox MB_ICONEXCLAMATION "$(notAdmin)"
			Abort
	ok_2:


 	ReadRegStr $R1 HKLM "SOFTWARE\Athletica\Athletica" "VERSION"
	StrCmp $R1 "" ok_3 warn
	warn:
		;MessageBox MB_YESNO|MB_DEFBUTTON2 "$(athleticaInstalled)" IDYES ok_3
		MessageBox MB_ICONEXCLAMATION "$(athleticaInstalled)"
		Abort
	ok_3:

	StrCmp $R0 "98" ok_5
	StrCmp $R0 "ME" ok_5

	; check if MySQL service already installed
	ReadRegStr $R1 HKLM "SYSTEM\CurrentControlSet\Services\MySQL" "ImagePath"
	StrCmp $R1 "" ok_4				; check if service path empty
		MessageBox MB_ICONEXCLAMATION "$(mysqlInstalled)"
		Abort
	ok_4:

	; check if Apache service already installed
	ReadRegStr $R1 HKLM "SYSTEM\CurrentControlSet\Services\Apache" "ImagePath"
	StrCmp $R1 "" ok_5				; check if service path empty
		MessageBox MB_ICONEXCLAMATION "$(apacheInstalled)"
		Abort
	ok_5:

	; check if msvcrt.dll required by "sed" is installed
	IfFileExists "$SYSDIR\msvcrt.dll" ok_6
		MessageBox MB_ICONEXCLAMATION "$(missingDLL)"
		Abort
	ok_6:

FunctionEnd


Function .onInstSuccess
   MessageBox MB_YESNO $(msg) IDNO NoStart
	SetShellVarContext all
   ExecShell open $SMPROGRAMS\Athletica\Athletica.lnk
   NoStart:
FunctionEnd


Function un.onInit
	;Language selection dialog
	Push ""
	Push ${LANG_GERMAN}
	Push Deutsch
	Push ${LANG_ENGLISH}
	Push English
	Push A ; A means auto count languages
	       ; for the auto count to work the first empty push (Push "") must remain
	LangDLL::LangDialog "$(langDlgTitle)" "$(langDlgText)"

	Pop $LANGUAGE
	StrCmp $LANGUAGE "cancel" 0 +2
		Abort
FunctionEnd


Function un.onGUIInit
	; check OS Version
	Call un.GetWindowsVersion
	Pop $R0
	StrCpy $R1 $R0 3			; truncate OS Version to three chars.
	Push $R1

	; check admin user
	StrCmp $R0 "98" ok_1
	StrCmp $R0 "ME" ok_1
		UserInfo::GetName
		IfErrors ok_1
		Pop $0
		UserInfo::GetAccountType
		Pop $1
		StrCmp $1 "Admin" ok_1
			MessageBox MB_ICONEXCLAMATION "$(un.notAdmin)"
			Abort
	ok_1:

FunctionEnd



Function postInfo 
	StrCpy $0 $(readme)
  	ExecWait "notepad.exe $0"
FunctionEnd


Function runSQL
	ExpandEnvStrings $R0 %COMSPEC%
	GetFullPathName /SHORT $R1 $INSTDIR\mysql\bin\mysql.exe
	GetFullPathName /SHORT $R2 $INSTDIR\www\athletica\sql\$0
	Exec `"$R0" /C "$R1" -u root athletica < $R2`
FunctionEnd

; GetWindowsVersion
;
; Windows Version (95, 98, ME, NT x.x, 2000, XP, .NET Server, Vista, Windows 7)
; or
; '' (Unknown Windows Version)
;
; Usage:
;   Call GetWindowsVersion
;   Pop $R0
;   ; at this point $R0 is "NT 4.0" or whatnot
Function GetWindowsVersion
   Push $R0
   Push $R1
   ReadRegStr $R0 HKLM "SOFTWARE\Microsoft\Windows NT\CurrentVersion" CurrentVersion
   StrCmp $R0 "" 0 lbl_winnt
   ; we are not NT.
   ReadRegStr $R0 HKLM SOFTWARE\Microsoft\Windows\CurrentVersion VersionNumber

   StrCpy $R1 $R0 1
   StrCmp $R1 '4' 0 lbl_error

   StrCpy $R1 $R0 3

   StrCmp $R1 '4.0' lbl_win32_95
   StrCmp $R1 '4.9' lbl_win32_ME lbl_win32_98

   lbl_win32_95:
     StrCpy $R0 '95'
   Goto lbl_done

   lbl_win32_98:
     StrCpy $R0 '98'
   Goto lbl_done

   lbl_win32_ME:
     StrCpy $R0 'ME'
   Goto lbl_done

   lbl_winnt:

     StrCpy $R1 $R0 1

     StrCmp $R1 '3' lbl_winnt_x
     StrCmp $R1 '4' lbl_winnt_x

     StrCpy $R1 $R0 3

     StrCmp $R1 '5.0' lbl_winnt_2000
     StrCmp $R1 '5.1' lbl_winnt_XP
     StrCmp $R1 '5.2' lbl_winnt_dotNET
     StrCmp $R1 '6.0' lbl_winnt_Vista
     StrCmp $R1 '6.1' lbl_winnt_7 lbl_error

     lbl_winnt_x:
       StrCpy $R0 "NT $R0" 6
     Goto lbl_done

     lbl_winnt_2000:
       Strcpy $R0 '2000'
     Goto lbl_done

     lbl_winnt_XP:
       Strcpy $R0 'XP'
     Goto lbl_done

     lbl_winnt_dotNET:
       Strcpy $R0 '.NET Server'
     Goto lbl_done

     lbl_winnt_Vista:
       Strcpy $R0 'Vista'
     Goto lbl_done

     lbl_winnt_7:
       Strcpy $R0 'Windows 7'
     Goto lbl_done

   lbl_error:
     Strcpy $R0 ''
   lbl_done:
   Pop $R1
   Exch $R0
FunctionEnd


; un.GetWindowsVersion
Function un.GetWindowsVersion
   Push $R0
   Push $R1
   ReadRegStr $R0 HKLM "SOFTWARE\Microsoft\Windows NT\CurrentVersion" CurrentVersion
   StrCmp $R0 "" 0 lbl_winnt
   ; we are not NT.
   ReadRegStr $R0 HKLM SOFTWARE\Microsoft\Windows\CurrentVersion VersionNumber

   StrCpy $R1 $R0 1
   StrCmp $R1 '4' 0 lbl_error

   StrCpy $R1 $R0 3

   StrCmp $R1 '4.0' lbl_win32_95
   StrCmp $R1 '4.9' lbl_win32_ME lbl_win32_98

   lbl_win32_95:
     StrCpy $R0 '95'
   Goto lbl_done

   lbl_win32_98:
     StrCpy $R0 '98'
   Goto lbl_done

   lbl_win32_ME:
     StrCpy $R0 'ME'
   Goto lbl_done

   lbl_winnt:

     StrCpy $R1 $R0 1

     StrCmp $R1 '3' lbl_winnt_x
     StrCmp $R1 '4' lbl_winnt_x

     StrCpy $R1 $R0 3

     StrCmp $R1 '5.0' lbl_winnt_2000
     StrCmp $R1 '5.1' lbl_winnt_XP
     StrCmp $R1 '5.2' lbl_winnt_dotNET
     StrCmp $R1 '6.0' lbl_winnt_Vista
     StrCmp $R1 '6.1' lbl_winnt_7 lbl_error

     lbl_winnt_x:
       StrCpy $R0 "NT $R0" 6
     Goto lbl_done

     lbl_winnt_2000:
       Strcpy $R0 '2000'
     Goto lbl_done

     lbl_winnt_XP:
       Strcpy $R0 'XP'
     Goto lbl_done

     lbl_winnt_dotNET:
       Strcpy $R0 '.NET Server'
     Goto lbl_done

     lbl_winnt_Vista:
       Strcpy $R0 'Vista'
     Goto lbl_done

     lbl_winnt_7:
       Strcpy $R0 'Windows 7'
     Goto lbl_done

   lbl_error:
     Strcpy $R0 ''
   lbl_done:
   Pop $R1
   Exch $R0
FunctionEnd



