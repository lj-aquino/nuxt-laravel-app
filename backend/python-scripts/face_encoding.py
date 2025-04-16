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

    # Convert backslashes to forward slashes (if needed)
    image_path = image_path.replace("\\", "/")

    # Check if the image exists
    if not os.path.exists(image_path):
        return {"success": False, "debug_logs": debug_logs}

    # Initialize the face detector and models
    detector = dlib.get_frontal_face_detector()
    
    # Ensure the shape predictor file exists
    shape_predictor_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\shape_predictor_68_face_landmarks.dat"
    if not os.path.exists(shape_predictor_path):
        return {"success": False, "debug_logs": debug_logs}
    sp = dlib.shape_predictor(shape_predictor_path)
    
    # Ensure the face recognition model file exists
    face_recognition_model_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\dlib_face_recognition_resnet_model_v1.dat"
    if not os.path.exists(face_recognition_model_path):
        return {"success": False, "debug_logs": debug_logs}
    facerec = dlib.face_recognition_model_v1(face_recognition_model_path)

    # Load the image
    image = cv2.imread(image_path)
    if image is None:
        return {"success": False, "debug_logs": debug_logs}

    # Convert the image to grayscale
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # Detect faces in the image
    faces = detector(gray)

    if len(faces) > 0:
        # Process the first detected face
        shape = sp(gray, faces[0])

        # Compute the face descriptor (encoding)
        face_descriptor = facerec.compute_face_descriptor(image, shape)

        # Convert the encoding to a list
        encoding_list = list(face_descriptor)
        
        return {
            "success": True,
            "encoding": encoding_list,
            "debug_logs": debug_logs,
        }
    else:
        return {"success": False, "debug_logs": debug_logs}

# Test the function
if __name__ == "__main__":
    # Ensure that the script receives the image path as a command-line argument
    if len(sys.argv) < 2:
        print("No image path provided.")
        sys.exit(1)

    # Get the image path from the command-line argument
    image_path = sys.argv[1]

    encoding_result = get_face_encoding(image_path)
    if encoding_result["success"]:
        # Print only the clean result in the desired format
        print(f"Face encoding: {encoding_result}")
    else:
        print("No face encoding generated.")