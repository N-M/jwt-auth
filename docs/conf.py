import os
import yaml

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
html_baseurl = os.environ.get("pages_root")
html_sidebars = {
    '**': ['versions.html'],
}

locale_dirs = ['locale/']   # path is example but recommended.
gettext_compact = False     # optional.

current_language = os.environ.get("current_language", "en")
current_version = os.environ.get("current_version")

build_all_docs = os.environ.get("build_all_docs")
pages_root = os.environ.get("pages_root", "")

html_context = {
  "current_language": current_language,
  "languages": [
     {"name": 'en', "url": pages_root+'/'+current_version+'/'+language},
  ],
  "current_version": { "name": current_version },
  "versions": {
     "tags": [],
     "branches": [
        {"name": "main", "url": pages_root}
     ]
  }
}

with open("versions.yaml", "r") as yaml_file:
    docs = yaml.safe_load(yaml_file)
    for language in docs[current_version].get('languages', []):
      found = next((item for item in html_context['languages'] if item['name'] == current_language), None)

      if(found is None):
        html_context['languages'].append({
          "name": language,
          "url": pages_root+'/'+current_version+'/'+language,
        })

      for version, details in docs.items():
        if version == "latest":
           continue

        html_context['versions']['tags'].append({
           "name": version,
           "url": pages_root+'/'+version+'/'+current_language,
        })
