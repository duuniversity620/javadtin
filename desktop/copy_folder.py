import shutil
import ctypes
import sys

src = r"C:\Users\Bravo\Desktop\11"
dst = r"C:\Users\Bravo\Desktop\12"

try:
    shutil.copytree(src, dst, dirs_exist_ok=True)

    ctypes.windll.user32.MessageBoxW(
        0,
        "Copy completed successfully.",
        "Success",
        0x40
    )

except Exception as e:
    ctypes.windll.user32.MessageBoxW(
        0,
        str(e),
        "Error",
        0x10
    )
    sys.exit(1)