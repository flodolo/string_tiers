#! /usr/bin/env python

'''
    This script reads list.txt and list.json, and check if there are missing files.
'''

import os
import json


def diff(a, b):
    b = set(b)
    return [aa for aa in a if aa not in b]


def print_array(list):
    if list:
        print('\n'.join(list))
    else:
        print('(no files)')


def main():
    data_folder = os.path.abspath(os.path.join(
        os.path.dirname(__file__), os.pardir, 'data'))

    with open(os.path.join(data_folder, 'list.json'), 'r') as f:
        json_data = json.load(f)

    filename = os.path.join(data_folder, 'list.txt')
    file_list = open(filename, 'r').read().splitlines()
    json_list = [item['file'] for item in json_data]

    print('\nList of files available in list.txt but missing in list.json')
    print_array(diff(file_list, json_list))
    print('\nList of files available in list.json but missing in list.txt')
    print_array(diff(json_list, file_list))

if __name__ == '__main__':
    main()
