
with open('storage/logs/laravel.log', 'rb') as f:
    f.seek(0, 2)
    size = f.tell()
    f.seek(max(0, size - 4000))
    print(f.read().decode('utf-8', errors='ignore'))
