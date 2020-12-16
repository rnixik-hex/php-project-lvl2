# Gendiff

Educational demo project.
It computes the difference between two files.

### Hexlet tests and linter status:
[![Actions Status](https://github.com/rnixik-hex/php-project-lvl2/workflows/hexlet-check/badge.svg)](https://github.com/rnixik-hex/php-project-lvl2/actions)
[![Linter](https://github.com/rnixik-hex/php-project-lvl2/workflows/Linter/badge.svg)](https://github.com/rnixik-hex/php-project-lvl1/actions)
[![Tests](https://github.com/rnixik-hex/php-project-lvl2/workflows/Tests/badge.svg)](https://github.com/rnixik-hex/php-project-lvl1/actions)
[![Maintainability](https://api.codeclimate.com/v1/badges/278a171cb379c16647ca/maintainability)](https://codeclimate.com/github/rnixik-hex/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/278a171cb379c16647ca/test_coverage)](https://codeclimate.com/github/rnixik-hex/php-project-lvl2/test_coverage)

### Supported formats are:

#### Input:
* json
* yml

#### Output:
* stylish
* plain
* json

## Usage

```bash
bin/gendiff -h                              
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: stylish]

```

## Demo

[![asciicast](https://asciinema.org/a/tIdILmOfhUKJqptMbGXTlmfNP.svg)](https://asciinema.org/a/tIdILmOfhUKJqptMbGXTlmfNP)
