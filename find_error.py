
import re

found = False
lines_to_print = 0

with open('storage/logs/laravel.log', 'r', encoding='utf-8', errors='ignore') as f:
    lines = f.readlines()
    # Get last 200 lines
    last_lines = lines[-200:]
    
    for line in last_lines:
        if 'local.ERROR' in line:
            print("--- ERROR FOUND ---")
            print(line.strip())
            lines_to_print = 20
            found = True
        elif lines_to_print > 0:
            print(line.strip())
            lines_to_print -= 1
