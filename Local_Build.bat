@echo off
SETLOCAL ENABLEDELAYEDEXPANSION
ECHO DEBUG MODE ON
ECHO+
::用于构建本地播放器环境
SET ROOT_D=.\
SET ROOT_F="googlec5308d0d9cb559ed.html","H.php","robots.txt","roadmap.txt","robots.txt"
SET CONFIG_D=.\local
SET CONFIG_F="Main.FNQ.php","Queue.php","Main.N1c0.php","Main.Flvcache.php"
SET PLAYER_D=.\p
SET PLAYER_F=".\ac",".\ni"
SET PMPAGE_D=.\wiki.d
SET PMPAGE_F=".\Queue",".\Parts",".\Acfun2",".\Bilibili2",".\DMR"
SET PROC_ARR=CONFIG,PLAYER,PMPAGE,ROOT
FOR %%a IN (%PROC_ARR%) DO (
    pushd !%%a_D!
    FOR %%b IN (!%%a_F!) DO (
        ECHO 	!CD! : DEL /F /Q %%b
    )
    popd
)
ECHO CLEAN UP  FIN.
SET LIBMOVE_D=Main,Site,System
FOR %%a IN (%LIBMOVE_D%) DO (
	ECHO 	!CD! : COPY ".\wiki.d\%%a\*.*" ".\shared\dmflib.d\%%a\"
	
	SET DELETE_D=".\wiki.d\%%a\",!DELETE_D!
)
SET DELETE_D=!DELETE_D!".\p\ac",".\p\ni"
ECHO MOVE PAGE FIN.
FOR %%a IN (%DELETE_D%) DO (
	ECHO 	!CD! : RD /S /Q %%a
)
ECHO DELETE DIR FIN.
PAUSE