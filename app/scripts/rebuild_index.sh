#! /usr/bin/env bash

current_folder=$(dirname "$0")

${current_folder}/restructure_json.py
${current_folder}/extract_data.py
