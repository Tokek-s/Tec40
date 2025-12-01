
$file = 'storage/logs/laravel.log'
$lines = Get-Content $file -Tail 50
$lines | ForEach-Object { $_ }
