import dlib
import cv2
import numpy as np

def get_face_encoding(image_path):
    print(f"Debug: Starting face encoding for image: {image_path}")

    # Initialize the face detector and models
    detector = dlib.get_frontal_face_detector()
    print("Debug: Face detector initialized.")
    
    # Update the file paths to the models in the new location
    sp = dlib.shape_predictor(r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\shape_predictor_68_face_landmarks.dat")
    print("Debug: Shape predictor initialized.")
    
    facerec = dlib.face_recognition_model_v1(r"C:\Users\LJ\Desktop\Academics\2nd Sem 2024-2025\nuxt-laravel-app\backend\app\Models\dlib_models\dlib_face_recognition_resnet_model_v1.dat")
    print("Debug: Face recognition model initialized.")

    # Load the image
    image = cv2.imread(image_path)
    if image is None:
        print("Debug: Failed to load the image.")
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

        return np.array(face_descriptor)
    else:
        print("Debug: No faces detected.")
        return None

# Test the function (for testing purpose)
if __name__ == "__main__":
    encoding = get_face_encoding("path_to_image.jpg")
    if encoding is not None:
        print(f"Debug: Face encoding: {encoding}")
    else:
        print("Debug: No face encoding generated.")
