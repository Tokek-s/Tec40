
import os

log_file = 'storage/logs/laravel.log'
if os.path.exists(log_file):
    with open(log_file, 'rb') as f:
        try:
            f.seek(-10000, 2) # Read last 10KB
        except OSError:
            f.seek(0)
        
        content = f.read()
        # Decode with replacement to avoid errors
        text = content.decode('utf-8', errors='replace')
        
        lines = text.splitlines()
        # Print last 50 lines
        for line in lines[-50:]:
            print(line)
