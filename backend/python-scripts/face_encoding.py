import dlib
import cv2
import numpy as np
import sys
import os
import json

def get_face_encoding(image_path):
    debug_logs = []  # Collect debug messages here

    def log_debug(message):
        # debug_logs.append(message)  # Add messages to the debug log
        print(message)  # Optionally print to the console for server-side debugging

    # log_debug(f"Starting face encoding for image: {image_path}")

    # Convert backslashes to forward slashes (if needed)
    image_path = image_path.replace("\\", "/")

    # Check if the image exists
    if not os.path.exists(image_path):
        # log_debug(f"Image file does not exist at {image_path}")
        return {"success": False, "debug_logs": debug_logs}

    # Initialize the face detector and models
    detector = dlib.get_frontal_face_detector()
    # log_debug("Face detector initialized.")
    
    # Ensure the shape predictor file exists
    shape_predictor_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\shape_predictor_68_face_landmarks.dat"
    if not os.path.exists(shape_predictor_path):
        # log_debug(f"Shape predictor model not found at {shape_predictor_path}")
        return {"success": False, "debug_logs": debug_logs}
    sp = dlib.shape_predictor(shape_predictor_path)
    # log_debug("Shape predictor initialized.")
    
    # Ensure the face recognition model file exists
    face_recognition_model_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\dlib_face_recognition_resnet_model_v1.dat"
    if not os.path.exists(face_recognition_model_path):
        # log_debug(f"Face recognition model not found at {face_recognition_model_path}")
        return {"success": False, "debug_logs": debug_logs}
    facerec = dlib.face_recognition_model_v1(face_recognition_model_path)
    # log_debug("Face recognition model initialized.")

    # Load the image
    # log_debug(f"Loading image from path: {image_path}")
    image = cv2.imread(image_path)
    if image is None:
        # log_debug(f"Failed to load the image from {image_path}.")
        return {"success": False, "debug_logs": debug_logs}
    # log_debug(f"Image loaded with shape: {image.shape}")

    # Convert the image to grayscale
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    # log_debug("Image converted to grayscale.")

    # Detect faces in the image
    faces = detector(gray)
    # log_debug(f"Detected {len(faces)} faces.")

    if len(faces) > 0:
        # Process the first detected face
        shape = sp(gray, faces[0])
        # log_debug("Facial landmarks detected.")

        # Compute the face descriptor (encoding)
        face_descriptor = facerec.compute_face_descriptor(image, shape)
        # log_debug("Face descriptor computed.")

        # Convert the encoding to a list (not including debug logs in the encoding field)
        encoding_list = list(face_descriptor)
        # log_debug("Face encoding converted to JSON.")
        return {
            "success": True,
            "encoding": encoding_list,  # Only the encoding data
            "debug_logs": debug_logs,  # Debug logs are separate
        }
    else:
        # log_debug("No faces detected.")
        return {"success": False, "debug_logs": debug_logs}

# Test the function (for testing purpose)
if __name__ == "__main__":
    # Ensure that the script receives the image path as a command-line argument
    if len(sys.argv) < 2:
        print("Debug: No image path provided.")
        sys.exit(1)

    # Get the image path from the command-line argument
    image_path = sys.argv[1]

    encoding = get_face_encoding(image_path)
    if encoding is not None:
        print(f"Debug: Face encoding: {encoding}")
    else:
        print("Debug: No face encoding generated.")
