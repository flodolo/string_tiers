#! /usr/bin/env python

'''
This script is used to convert a list of files generated in l10n repository
with to a JSON structure
find browser devtools dom mobile netwerk security services toolkit -type f > list.txt
'''

import os
import json


def main():
    json_data = []

    data_folder = os.path.abspath(os.path.join(
        os.path.dirname(__file__), os.pardir, 'data'))

    file_list = open(os.path.join(data_folder, 'list.txt'), 'r').read().splitlines()

    for file_name in file_list:
        if file_name.startswith('devtools/'):
            tier = '3'
            module = 'devtools'
        else:
            tier = ''
            module = ''
        record = {
            'file': file_name,
            'module': module,
            'tier': tier,
            'notes': '',
        }
        json_data.append(record)

    f = open(os.path.join(data_folder, 'list.json'), 'w')
    f.write(json.dumps(json_data, sort_keys=True, indent=2))
    f.close()


if __name__ == '__main__':
    main()
