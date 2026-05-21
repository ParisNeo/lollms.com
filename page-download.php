<?php
/**
 * Template Name: Download Page
 * Description: Simplified direct repository-first install instructions.
 */
get_header(); 
?>

<main class="nexus-container" style="padding: 80px 0;">
    <header style="text-align: center; margin-bottom: 60px;">
        <h1 style="font-size: 3.5rem; margin-bottom: 15px;">Install LoLLMs</h1>
        <p style="color: var(--text-dim); font-size: 1.25rem;">One tool to rule them all. Clone the repository and execute the provided setup script.</p>
    </header>

    <div class="nexus-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <!-- WINDOWS -->
        <div style="padding: 30px; border: 1px solid var(--border); border-radius: 16px; background: var(--bg-panel);">
            <h2 style="margin-top:0;">🪟 Windows</h2>
            <p style="color: var(--text-dim); margin-bottom: 20px;">Clone the repo and run the batch file.</p>
            <pre style="background: #000; padding: 15px; border-radius: 8px; color: #4ade80; font-size: 0.85rem; overflow-x: auto;">
git clone https://github.com/ParisNeo/lollms-webui.git
cd lollms-webui
run_windows.bat</pre>
        </div>

        <!-- LINUX -->
        <div style="padding: 30px; border: 1px solid var(--border); border-radius: 16px; background: var(--bg-panel);">
            <h2 style="margin-top:0;">🐧 Linux</h2>
            <p style="color: var(--text-dim); margin-bottom: 20px;">Clone and execute the bash script.</p>
            <pre style="background: #000; padding: 15px; border-radius: 8px; color: #4ade80; font-size: 0.85rem; overflow-x: auto;">
git clone https://github.com/ParisNeo/lollms-webui.git
cd lollms-webui
chmod +x run.sh
./run.sh</pre>
        </div>

        <!-- MAC -->
        <div style="padding: 30px; border: 1px solid var(--border); border-radius: 16px; background: var(--bg-panel);">
            <h2 style="margin-top:0;">🍎 MacOS</h2>
            <p style="color: var(--text-dim); margin-bottom: 20px;">Clone and execute the bash script.</p>
            <pre style="background: #000; padding: 15px; border-radius: 8px; color: #4ade80; font-size: 0.85rem; overflow-x: auto;">
git clone https://github.com/ParisNeo/lollms-webui.git
cd lollms-webui
chmod +x run.sh
./run.sh</pre>
        </div>
    </div>

    <div style="margin-top: 60px; text-align: center; border-top: 1px solid var(--border); padding-top: 40px;">
        <a href="https://github.com/ParisNeo/lollms-webui" target="_blank" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">View Repository on GitHub</a>
    </div>
</main>

<?php get_footer(); ?>