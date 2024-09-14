"""config file for sphinx"""

import os
import yaml

# Configuration file for the Sphinx documentation builder.
#
# For the full list of built-in configuration values, see the documentation:
# https://www.sphinx-doc.org/en/master/usage/configuration.html

# -- Project information -----------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#project-information

project = "JWT-Auth"
copyright = "2024, James Read"
author = "James Read"

# -- General configuration ---------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#general-configuration

extensions = [
    "sphinx.ext.githubpages",
    "sphinx_multiversion",
]
templates_path = ["_templates"]
exclude_patterns = ["_build", "Thumbs.db", ".DS_Store", "bin", "lib", "lib64"]

# -- Options for HTML output -------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#options-for-html-output

html_theme = "sphinx_rtd_theme"
html_static_path = []
html_baseurl = os.environ.get("pages_root")
html_sidebars = {
    "**": ["versions.html"],
}

locale_dirs = ["locale/"]  # path is example but recommended.
gettext_compact = False  # optional.

language = os.environ.get("current_language")
version = os.environ.get("current_version")

build_all_docs = os.environ.get("build_all_docs")
pages_root = os.environ.get("pages_root", "/")

html_context = {
    "current_language": language,
    "languages": [
        {"name": language, "url": f"/{version}/{language}"},
    ],
    "current_version": {"name": version},
    "versions": {"tags": [], "branches": [{"name": "main", "url": pages_root}]},
}


def meta(current_language: str) -> None:
    """
    adds meta data to html_context versions
    """
    with open("versions.yaml", "r", encoding="UTF-8") as yaml_file:
        docs = yaml.safe_load(yaml_file)
        for lang in docs[version].get("languages", []):
            found = next(
                (item for item in html_context["languages"] if item["name"] == lang),
                None,
            )
            if found is None:
                html_context["languages"].append(
                    {
                        "name": lang,
                        "url": f"/{version}/{lang}",
                    }
                )

        for label, details in docs.items():
            html_context["versions"]["tags"].append(
                {
                    "name": details.get("tag"),
                    "url": f"/jwt-auth/{label}/{current_language}",
                }
            )


meta(language)
