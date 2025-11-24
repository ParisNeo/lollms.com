# LoLLMs.com - The Nexus

> **One Tool to Rule Them All.**  
> The official web interface and future coordination hub for the Lord of Large Language Multimodal Systems (LoLLMs).

## 🌍 The Vision
LoLLMs is evolving from a local AI orchestrator into a distributed, peer-to-peer AI social network.
**LoLLMs.com** is not just a landing page. It is designed to be the **Nexus Node**:
1.  **Distribution Point:** The trusted source for LoLLMs binaries and bindings.
2.  **Directory Server:** A registry for users to find other "LoLLMs Islands" (local instances).
3.  **Economy Hub:** (Future) A clearinghouse for compute exchange and P2P interactions.

## 🛠 Architecture
This repository acts as a **WordPress App-Theme**. We bypass standard WP bloat in favor of raw performance and API-first design.

- **Stack:** PHP 8.1+, WordPress (Headless-ready), HTMX, Vanilla JS.
- **API:** Custom REST Endpoints (namespace: `lollms/v1`).
- **Styling:** CSS Variables (Dark Mode Native).

## 🚀 Installation (For Developers)
1.  Clone this repo into your WordPress themes directory:
    ```bash
    cd wp-content/themes/
    git clone https://github.com/ParisNeo/lollms.com.git lollms-nexus
    ```
2.  Activate the theme in WordPress Admin.
3.  Set `Permalinks` to "Post name" (Required for the API routing).

## 🤝 Contribution
We are building the "Internet of AIs."
- **Junior Devs:** Help with CSS, responsiveness, and translations.
- **Senior Devs:** Help with the `Hive_API` implementation and P2P handshaking protocols.

## 📜 License
Apache 2.0 - Free, Open, Forever.
