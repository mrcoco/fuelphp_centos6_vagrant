#!/bin/sh

CURRENT_DIR=`dirname "${0}"`
ROOT_DIR=$CURRENT_DIR"/.."

cd $ROOT_DIR

oil refine migrate
