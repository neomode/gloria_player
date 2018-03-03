@echo off

echo Step 1 of 2: Waiting a few seconds before starting the Gloria Player

"C:\windows\system32\ping" -n 5 -w 1000 127.0.0.1 >NUL

echo Step 2 of 5: Waiting a few more seconds before starting ...

"C:\windows\system32\ping" -n 5 -w 1000 127.0.0.1 >NUL

echo Final 'invisible' step: Starting the browser, Finally...

"C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" --chrome  --start-fullscreen http://127.0.0.1/gloria/player --incognito --disable-pinch --overscroll-history-navigation=0

exit