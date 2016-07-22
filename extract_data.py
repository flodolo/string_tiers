#! /usr/bin/env python

'''
Extract information from list.json and store it as index.md
'''

import json

def main():
    with open('list.json') as data_file:
        json_data = json.load(data_file)

    modules = {}
    module_names = []
    file_notes = {}

    for element in json_data:
        current_module = element['module']
        if current_module not in modules:
            modules[current_module] = {
                'tier': element['tier'],
                'files': [element['file']]
            }
            module_names.append(current_module)
        else:
            modules[current_module]['files'].append(element['file'])
            if element['tier'] != modules[current_module]['tier']:
                print '{0} has a different tier'.format(element['file'])

        # Store notes separately
        if element['notes']:
            file_notes[element['file']] = element['notes']

        # Check if JSON has some weird values
        if current_module == '':
            print '{0} has no module'.format(element['file'])
        if element['tier'] == '':
            print '{0} has an empty tier'.format(element['file'])

    module_names.sort()

    file_content = []
    file_content.append('## Modules\n')
    file_content.append('| Module Name | Tier | Files |')
    file_content.append('| ----------- | ---- | ----- |')

    for module_name in module_names:
        file_content.append(
            '| {0} | {1} | {2} |'.format(
                module_name, modules[module_name]['tier'],
                len(modules[module_name]['files'])))

    file_content.append('\n## Modules details\n')
    file_content.append('| Module | Tier | File | Notes |')
    file_content.append('| ------ | ---- | ---- | ----- |')

    for module_name in module_names:
        for file_name in modules[module_name]['files']:
            notes = '' if file_name not in file_notes else file_notes[file_name]
            file_content.append('| <sub>{0}</sub> | <sub>{1}</sub> | <sub>{2}</sub> | <sub>{3}</sub> |'.format(
                module_name, modules[module_name]['tier'],
                file_name, notes))

    with open('README.md', 'w') as f:
        f.write('\n'.join(file_content))


if __name__ == '__main__':
    main()
