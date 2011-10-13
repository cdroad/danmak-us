@echo off
pause
echo 清除项目文件
call :RemoveDir .\settings
call :DeleteFiles .\.buildpath .\.gitignore .\.project 
echo 清理辅助文件
call :DeleteFiles .\APIMap.txt .\FindAll.cmd .\wiki.d\.pageindex
echo 清理非公开文件
call :DeleteFiles .\googlec5308d0d9cb559ed.html .\FindAll.cmd
pushd local
call :DeleteFiles .\Main.Talk.php .\Queue.php .\Main.FNQ.php .\Main.Flvcache.php
popd
echo 清理无用数据文件
call :clearDir .\uploads\Acfun2
call :clearDir .\uploads\Bilibili2
call :clearDir .\wiki.d\Acfun2
call :clearDir .\wiki.d\Bilibili2
call :clearDir .\wiki.d\DMR
call :RemoveDir .\wiki.d\Queue
call :RemoveDir .\wiki.d\Site
call :RemoveDir .\wiki.d\SiteAdmin
echo 清理备份文件
for /R .\ %%a in (*.bak) do (
    call :DeleteFiles  %%a
)


PAUSE
GOTO :EOF

:clearDir
echo    清空 "%~f1"
del "%~f1\*.*"
GOTO :EOF

:RemoveDir
echo    移除 "%~f1"
rd "%~f1" /q
GOTO :EOF

:DeleteFiles
if "%~x1"=="" GOTO :EOF
echo    删除 "%~x1"
del "%~f1" /F /Q
shift /1
GOTO :DeleteFiles