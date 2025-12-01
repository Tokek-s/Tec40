
import os

log_file = 'storage/logs/laravel.log'
if os.path.exists(log_file):
    with open(log_file, 'rb') as f:
        try:
            f.seek(-20000, 2) # Read last 20KB
        except OSError:
            f.seek(0)
        
        content = f.read()
        text = content.decode('utf-8', errors='replace')
        
        # Find all occurrences of "Error en inscripción"
        parts = text.split('Error en inscripción: ')
        if len(parts) > 1:
            print("LAST ERROR FOUND:")
            print(parts[-1][:500]) # Print first 500 chars of the last error
        else:
            print("No specific error found in the last 20KB")
