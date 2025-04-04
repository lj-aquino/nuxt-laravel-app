import dlib
import cv2
import numpy as np
import sys
import os
import json

def get_face_encoding(image_path):
    print(f"Debug: Starting face encoding for image: {image_path}")

    # Convert backslashes to forward slashes (if needed)
    image_path = image_path.replace("\\", "/")

    # Check if the image exists
    if not os.path.exists(image_path):
        print(f"Debug: Image file does not exist at {image_path}")
        sys.exit(1)  # Exit if the image doesn't exist

    # Initialize the face detector and models
    detector = dlib.get_frontal_face_detector()
    print("Debug: Face detector initialized.")
    
    # Ensure the shape predictor file exists
    shape_predictor_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\shape_predictor_68_face_landmarks.dat"
    if not os.path.exists(shape_predictor_path):
        print(f"Debug: Shape predictor model not found at {shape_predictor_path}")
        sys.exit(1)  # Exit if the shape predictor model is missing
    sp = dlib.shape_predictor(shape_predictor_path)
    print("Debug: Shape predictor initialized.")
    
    # Ensure the face recognition model file exists
    face_recognition_model_path = r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\dlib_face_recognition_resnet_model_v1.dat"
    if not os.path.exists(face_recognition_model_path):
        print(f"Debug: Face recognition model not found at {face_recognition_model_path}")
        sys.exit(1)  # Exit if the face recognition model is missing
    facerec = dlib.face_recognition_model_v1(face_recognition_model_path)
    print("Debug: Face recognition model initialized.")

    # Load the image
    print(f"Debug: Loading image from path: {image_path}")
    image = cv2.imread(image_path)
    if image is None:
        print(f"Debug: Failed to load the image from {image_path}.")
        return None
    print(f"Debug: Image loaded with shape: {image.shape}")

    # Convert the image to grayscale
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    print("Debug: Image converted to grayscale.")

    # Detect faces in the image
    faces = detector(gray)
    print(f"Debug: Detected {len(faces)} faces.")

    if len(faces) > 0:
        # Process the first detected face
        shape = sp(gray, faces[0])
        print("Debug: Facial landmarks detected.")

        # Compute the face descriptor (encoding)
        face_descriptor = facerec.compute_face_descriptor(image, shape)
        print("Debug: Face descriptor computed.")

        # Convert the encoding to JSON format
        encoding_json = json.dumps(list(face_descriptor))
        print("Debug: Face encoding converted to JSON.")
        return encoding_json
    else:
        print("Debug: No faces detected.")
        return None

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
