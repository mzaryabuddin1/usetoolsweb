"""
Background remover API for cPanel Python app (Passenger + Flask + rembg).

Deploy to: /home/vvehirec/usetoolswebpy/
App URL:    https://usetoolsweb.com/asdsadsadwq
"""
import os
import sys
from io import BytesIO

from flask import Flask, jsonify, request, send_file

app = Flask(__name__)
application = app  # Passenger entry point

MAX_BYTES = int(os.environ.get('BG_REMOVE_MAX_BYTES', 15 * 1024 * 1024))
API_SECRET = os.environ.get('BG_REMOVE_API_SECRET', '').strip()
ALLOWED = {'image/jpeg', 'image/png', 'image/webp'}


def check_secret():
    if not API_SECRET:
        return True
    return request.headers.get('X-BG-Remove-Key', '') == API_SECRET


@app.route('/')
def health():
    return jsonify({
        'ok': True,
        'service': 'usetoolsweb-bg-remove',
        'python': sys.version.split()[0],
    })


@app.route('/remove', methods=['POST'])
def remove_bg():
    if not check_secret():
        return jsonify({'ok': False, 'error': 'Unauthorized'}), 403

    if 'image' not in request.files:
        return jsonify({'ok': False, 'error': 'No image uploaded. Use field name "image".'}), 400

    upload = request.files['image']
    if not upload or upload.filename == '':
        return jsonify({'ok': False, 'error': 'Empty upload.'}), 400

    data = upload.read()
    if not data:
        return jsonify({'ok': False, 'error': 'Empty file.'}), 400
    if len(data) > MAX_BYTES:
        return jsonify({'ok': False, 'error': 'Image too large.'}), 413

    mime = upload.mimetype or ''
    if mime not in ALLOWED:
        return jsonify({'ok': False, 'error': 'Use JPG, PNG, or WebP.'}), 400

    try:
        from rembg import remove
        result = remove(data)
    except ImportError:
        return jsonify({'ok': False, 'error': 'rembg not installed. Run: pip install rembg'}), 503
    except Exception as exc:
        return jsonify({'ok': False, 'error': str(exc)}), 500

    if not result:
        return jsonify({'ok': False, 'error': 'Background removal failed.'}), 500

    return send_file(
        BytesIO(result),
        mimetype='image/png',
        as_attachment=False,
        download_name='no-background.png',
    )


if __name__ == '__main__':
    app.run(debug=True, port=5000)
