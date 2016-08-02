#! /usr/bin/env python

'''
Create list_meta.json from list.json.
'''

import json


def main():
    with open('list.json') as data_file:
        json_data = json.load(data_file)

    new_content = {
        'components': [],
        'files': {},
        'modules': {}
    }

    modules = {}
    # Use lists to be able to sort them
    module_names = []
    component_names = []

    for element in json_data:
        current_module = element['module']
        if current_module not in modules:
            modules[current_module] = element['tier']
            module_names.append(current_module)
    module_names.sort()
    new_content['modules'] = modules

    for module_name in module_names:
        component_name = module_name.split('/')[0]
        if component_name not in component_names:
            component_names.append(component_name)
    component_names.sort()
    new_content['components'] = component_names

    for element in json_data:
        new_content['files'][element['file']] = {
            'module': element['module'],
            'notes': element['notes'],
            'tier': element['tier'],
        }

    with open('list_meta.json', 'w') as f:
        f.write(json.dumps(new_content, sort_keys=True, indent=2))


if __name__ == '__main__':
    main()
