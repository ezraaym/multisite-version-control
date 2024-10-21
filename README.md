# Multisite Version Control

## Overview
Multisite Version Control is a custom WordPress plugin that enables version control across WordPress multisite networks. It tracks changes to themes, plugins, and the database using Git, making it easier to manage updates and maintain consistency across sites.

## Features
- Git-based version control for themes and plugins.
- Database change tracking and logging.
- Multisite-compatible functionality.
- Custom hooks to manage changes and commits.
- Admin interface to manage version control settings.

## Installation
1. Clone the repository to your local environment:
    ```bash
    git clone https://github.com/yourusername/multisite-version-control.git
    ```
2. Upload the `multisite-version-control` folder to the `wp-content/plugins` directory of your WordPress installation.
3. Activate the plugin from the WordPress dashboard.

## Usage
- Configure your Git repository and SSH access on the server.
- Use the plugin’s admin page in the network admin to manage settings.
- View logs of database changes and recent Git commits from the dashboard.

## Development
- Make sure you have Git and SSH access configured on your local machine.
- Use `git pull` and `git push` to manage updates between your local environment and the GitHub repository.
- Periodically back up the database and commit changes using the provided tools.

## Contributing
Feel free to fork the repository and submit pull requests. Contributions are welcome!

## License
MIT License. See `LICENSE` for more information.
