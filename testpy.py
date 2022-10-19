import subprocess

proc = subprocess.Popen("php testphp.php", shell=True, stdout=subprocess.PIPE)
response = proc.stdout.read()
print(response)
