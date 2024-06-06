# Configuration file for the Sphinx documentation builder.
#
# For the full list of built-in configuration values, see the documentation:
# https://www.sphinx-doc.org/en/master/usage/configuration.html

# -- Project information -----------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#project-information

project = 'jwt-auth'
copyright = '2024, James Read'
author = 'James Read'
release = '2.0'

# -- General configuration ---------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#general-configuration

extensions = [
    'sphinx.ext.githubpages',
    'sphinx_multiversion',
]
templates_path = ['_templates']
exclude_patterns = ['_build', 'Thumbs.db', '.DS_Store', 'bin', 'lib']
language = 'en'

# -- Options for HTML output -------------------------------------------------
# https://www.sphinx-doc.org/en/master/usage/configuration.html#options-for-html-output

html_theme = 'sphinx_rtd_theme'
html_static_path = ['_static']
html_baseurl = ''
html_sidebars = {
    '**': ['versions.html'],
}

locale_dirs = ['locale/']   # path is example but recommended.
gettext_compact = False     # optional.

html_context = {
    "current_version": {"name": "latest"},
    "versions": {
        "tags": [
          {"name": "2.0", "url": "./latest"},
        ],
    },
}
