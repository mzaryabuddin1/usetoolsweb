import sys
import os

# cPanel Python app — add project dir to path
sys.path.insert(0, os.path.dirname(__file__))

from app import application
