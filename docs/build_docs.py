"""build script for sphinx"""

import os
import subprocess
import yaml


def main():
    """main entrypoint"""

    os.environ["build_all_docs"] = str(True)
    os.environ["pages_root"] = "https://jimtools.github.io/jwt-auth/"

    # manually the main branch build in the current supported languages
    build_doc("latest", "en", "main")
    move_dir("./_build/html/*", "../pages/")

    # reading the yaml file
    with open("versions.yaml", "r", encoding="utf-8") as yaml_file:
        docs = yaml.safe_load(yaml_file)

    if docs is None:
        return

    # and looping over all values to call our build with version, language and its tag
    for version, details in docs.items():
        for language in details.get("languages", []):
            build_doc(version, language, details.get("tag"))
            move_dir("_build/html/*", f"../pages/{version}/{language}/")


def build_doc(version: str, language: str, tag: str) -> None:
    """
    single build step, which keeps conf.py and versions.yaml at the main branch
    in generall we use environment variables to pass values to conf.py, see below
    and runs the build as we did locally
    """
    os.environ["current_version"] = version
    os.environ["current_language"] = language
    subprocess.run("git checkout " + tag, shell=True, check=True)
    subprocess.run("git checkout main -- conf.py", shell=True, check=True)
    subprocess.run("git checkout main -- versions.yaml", shell=True, check=True)
    os.environ["SPHINXOPTS"] = f"-D language='{language}'"
    subprocess.run("make html", shell=True, check=True)


def move_dir(src: str, dst: str) -> None:
    """
    a move dir method because we run multiple builds and bring the html folders
    to a location which we then push to github pages
    """
    os.makedirs(dst, exist_ok=True)
    subprocess.run(f"mv {src} {dst}", shell=True, check=True)


if __name__ == "__main__":
    main()
