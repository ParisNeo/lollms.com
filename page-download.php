<?php
/**
 * Template Name: Download Wizard
 * Description: The interactive installer generator for LoLLMs.
 */

get_header(); 
?>

<!-- Custom Styles for the Wizard (Scoped to this page) -->
<style>
    .wizard-wrapper {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 40px;
        margin-top: 40px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .step-title {
        font-size: 1.1rem;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 10px;
    }

    .grid-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-bottom: 40px;
    }

    .option-btn {
        background: rgba(2, 6, 23, 0.5);
        border: 1px solid var(--border);
        color: var(--text-main);
        padding: 20px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        font-weight: 600;
    }

    .option-btn:hover {
        border-color: var(--primary);
        background: rgba(99, 102, 241, 0.1);
    }

    .option-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }

    .terminal-box {
        background: #000;
        border: 1px solid #333;
        border-radius: 8px;
        padding: 20px;
        font-family: 'Courier New', monospace;
        color: #4ade80; /* Green */
        position: relative;
        display: none; /* Hidden by default */
        animation: slideUp 0.3s ease-out;
    }

    .copy-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #333;
        color: #fff;
        border: none;
        padding: 5px 12px;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
    }
    .copy-btn:hover { background: #555; }

    .info-alert {
        margin-top: 20px;
        padding: 15px;
        background: rgba(99, 102, 241, 0.1);
        border-left: 3px solid var(--primary);
        color: var(--text-dim);
        font-size: 0.9rem;
        display: none;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<main class="nexus-container" style="padding: 80px 0;">
    
    <header style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 3.5rem; margin-bottom: 15px;">Install LoLLMs</h1>
        <p style="color: var(--text-dim); font-size: 1.2rem;">Select your configuration to generate the launch codes.</p>
    </header>

    <div class="wizard-wrapper">
        
        <!-- STEP 1: OS -->
        <div class="wizard-step">
            <div class="step-title">1. Operating System</div>
            <div class="grid-options">
                <button class="option-btn" onclick="selectOS('windows')" id="btn-windows">
                    <span style="font-size: 1.5em; display:block; margin-bottom:5px;">🪟</span> Windows
                </button>
                <button class="option-btn" onclick="selectOS('linux')" id="btn-linux">
                    <span style="font-size: 1.5em; display:block; margin-bottom:5px;">🐧</span> Linux
                </button>
                <button class="option-btn" onclick="selectOS('mac')" id="btn-mac">
                    <span style="font-size: 1.5em; display:block; margin-bottom:5px;">🍎</span> MacOS
                </button>
            </div>
        </div>

        <!-- STEP 2: Hardware (Hidden initially) -->
        <div class="wizard-step" id="step-hardware" style="display: none;">
            <div class="step-title">2. Hardware Acceleration</div>
            <div class="grid-options">
                <button class="option-btn" onclick="selectHW('nvidia')" id="btn-nvidia">NVIDIA GPU</button>
                <button class="option-btn" onclick="selectHW('amd')" id="btn-amd">AMD GPU</button>
                <button class="option-btn" onclick="selectHW('cpu')" id="btn-cpu">CPU Only</button>
                <button class="option-btn" onclick="selectHW('apple')" id="btn-apple" style="display:none;">Apple Silicon</button>
            </div>
        </div>

        <!-- STEP 3: Output -->
        <div id="result-area">
            
            <!-- Terminal Command Output -->
            <div id="terminal-output" class="terminal-box">
                <button class="copy-btn" onclick="copyCommand()">Copy</button>
                <div id="cmd-text" style="white-space: pre-wrap; word-break: break-all;"></div>
            </div>

            <!-- Windows EXE Button -->
            <div id="windows-dl-btn" style="display:none; text-align:center; margin-top: 30px;">
                <a href="https://github.com/ParisNeo/lollms-webui/releases/latest/download/win_install.bat" class="btn btn-glow" style="font-size: 1.2rem; padding: 15px 40px;">
                    ⇩ Download Installer (.bat)
                </a>
            </div>

            <!-- Note Box -->
            <div id="info-box" class="info-alert"></div>
        </div>

    </div>
</main>

<script>
    let currentOS = null;

    function selectOS(os) {
        currentOS = os;
        
        // UI: Highlight OS
        document.querySelectorAll('#btn-windows, #btn-linux, #btn-mac').forEach(b => b.classList.remove('active'));
        document.getElementById('btn-' + os).classList.add('active');

        // Reset subsequent steps
        document.getElementById('step-hardware').style.display = 'none';
        document.getElementById('terminal-output').style.display = 'none';
        document.getElementById('windows-dl-btn').style.display = 'none';
        document.getElementById('info-box').style.display = 'none';
        
        // Reset HW selection
        document.querySelectorAll('#step-hardware .option-btn').forEach(b => b.classList.remove('active'));

        // Routing Logic
        if (os === 'windows') {
            // Windows skips hardware check for the web installer
            document.getElementById('windows-dl-btn').style.display = 'block';
            showInfo("Download the .bat file. Windows SmartScreen may warn you because we are open source and don't buy expensive certificates. Click 'More Info' -> 'Run Anyway'.");
        } 
        else if (os === 'linux') {
            document.getElementById('step-hardware').style.display = 'block';
            document.getElementById('btn-apple').style.display = 'none';
            document.getElementById('btn-nvidia').style.display = 'block';
            document.getElementById('btn-amd').style.display = 'block';
        } 
        else if (os === 'mac') {
            document.getElementById('step-hardware').style.display = 'block';
            document.getElementById('btn-nvidia').style.display = 'none';
            document.getElementById('btn-amd').style.display = 'none';
            document.getElementById('btn-apple').style.display = 'block';
        }
    }

    function selectHW(hw) {
        // UI: Highlight HW
        document.querySelectorAll('#step-hardware .option-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('btn-' + hw).classList.add('active');

        generateCommand(hw);
    }

    function generateCommand(hw) {
        const termBox = document.getElementById('terminal-output');
        const cmdText = document.getElementById('cmd-text');
        
        let command = "";
        let info = "";

        // Linux Logic
        if (currentOS === 'linux') {
            command = "curl -fsSL https://raw.githubusercontent.com/ParisNeo/lollms-webui/main/webui.sh | sh";
            info = "Open your terminal and paste this command. It will auto-detect your " + hw.toUpperCase() + " drivers.";
        }
        // Mac Logic
        else if (currentOS === 'mac') {
            command = "curl -fsSL https://raw.githubusercontent.com/ParisNeo/lollms-webui/main/webui.sh | sh";
            info = "Works on macOS 12+. Ensure you have Python 3.10 installed via Homebrew.";
        }

        if (command) {
            cmdText.innerText = command;
            termBox.style.display = 'block';
            showInfo(info);
        }
    }

    function showInfo(msg) {
        const box = document.getElementById('info-box');
        box.innerText = msg;
        box.style.display = 'block';
    }

    function copyCommand() {
        const text = document.getElementById('cmd-text').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.querySelector('.copy-btn');
            const original = btn.innerText;
            btn.innerText = 'Copied!';
            setTimeout(() => btn.innerText = original, 2000);
        });
    }
</script>

<?php get_footer(); ?>
