#! /usr/bin/env python

'''
Extract information from list.json and store it as index.md
'''

import os
import json


def main():
    main_folder = os.path.abspath(os.path.join(
        os.path.dirname(__file__), os.pardir))
    data_folder = os.path.join(main_folder, 'data')

    with open(os.path.join(data_folder, 'list_meta.json')) as data_file:
        json_data = json.load(data_file)


    # Store all file names contained in each module
    modules = {}
    for file_name, file_data in json_data['files'].iteritems():
        current_module = file_data['module']
        if current_module not in modules:
            modules[current_module] = []
        modules[current_module].append(file_name)

    file_content = []
    file_content.append('## Modules\n')
    file_content.append('| Module Name | Tier | Files |')
    file_content.append('| ----------- | ---- | ----- |')

    module_names = json_data['modules'].keys()
    module_names.sort()

    for module_name in module_names:
        file_content.append(
            '| {0} | {1} | {2} |'.format(
                module_name, json_data['modules'][module_name],
                len(modules[module_name])))

    file_content.append('\n## Modules details\n')
    file_content.append('| Module | Tier | File | Notes |')
    file_content.append('| ------ | ---- | ---- | ----- |')

    for module_name in module_names:
        file_names = modules[module_name]
        file_names.sort()
        for file_name in file_names:
            file_content.append('| <sub>{0}</sub> | <sub>{1}</sub> | <sub>{2}</sub> | <sub>{3}</sub> |'.format(
                module_name, json_data['modules'][module_name],
                file_name, json_data['files'][file_name]['notes']))

    with open(os.path.join(main_folder, 'README.md'), 'w') as f:
        f.write('\n'.join(file_content))


if __name__ == '__main__':
    main()
