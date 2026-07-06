#!/usr/bin/env python3
"""Remove image background using rembg. Usage: rembg-remove.py <input> <output>"""
import sys

def main() -> int:
    if len(sys.argv) != 3:
        print('Usage: rembg-remove.py <input> <output>', file=sys.stderr)
        return 1

    inp, out = sys.argv[1], sys.argv[2]

    try:
        from rembg import remove
    except ImportError:
        print('rembg is not installed. Run: pip install rembg', file=sys.stderr)
        return 2

    try:
        with open(inp, 'rb') as f:
            data = f.read()
        result = remove(data)
        with open(out, 'wb') as f:
            f.write(result)
    except Exception as exc:
        print(str(exc), file=sys.stderr)
        return 3

    return 0


if __name__ == '__main__':
    sys.exit(main())
