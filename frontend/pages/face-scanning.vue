<template>
    <div>
      <h1>Face Scanning</h1>
      <div v-if="isConnected">
        <p>Connection to DroidCam is successful!</p>
        <video ref="video" id="video" width="640" height="480" autoplay></video>
        <button @click="captureFaceEncoding">Capture Face Encoding</button>
      </div>
      <div v-else>
        <p>Connecting to DroidCam...</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isConnected: false,
        faceEncoding: [],
      };
    },
    mounted() {
      this.connectToDroidCam();
    },
    methods: {
      connectToDroidCam() {
        const videoElement = this.$refs.video;
        const constraints = {
          video: {
            deviceId: {
              exact: "http://192.168.1.113:4747/video" // DroidCam IP URL
            }
          }
        };
  
        navigator.mediaDevices.getUserMedia(constraints)
          .then((stream) => {
            videoElement.srcObject = stream;
            this.isConnected = true;
          })
          .catch((error) => {
            console.error("Error connecting to DroidCam:", error);
            this.isConnected = false;
          });
      },
  
      captureFaceEncoding() {
        // Call your method to capture the face encoding
        this.processFaceEncoding();
      },
  
      processFaceEncoding() {
        const videoElement = this.$refs.video;
        // Simulating face encoding extraction from the video frame
        // In practice, you will use a method that extracts actual face encodings from the video feed
  
        // Here is a sample face encoding array (similar to your provided example):
        const sampleFaceEncoding = [
          -0.12127532809972763, 0.06478552520275116, 0.07996156811714172, // and so on
          0.027136305347085
        ];
  
        this.faceEncoding = sampleFaceEncoding; // Store it in data for now
        console.log('Face Encoding:', this.faceEncoding);
  
        // You would typically call an API here to save the encoding
        //this.saveFaceEncodingToDB();
      },
  
    //   async saveFaceEncodingToDB() {
    //     // Assuming you have Supabase set up and want to save the face encoding
    //     const { data, error } = await this.$supabase
    //       .from('face_encodings')
    //       .insert([
    //         {
    //           student_number: '2021-07092',  // Use actual student number here
    //           encoding: this.faceEncoding,   // This is the face encoding captured
    //         }
    //       ]);
        
    //     if (error) {
    //       console.error('Error saving face encoding:', error);
    //     } else {
    //       console.log('Face encoding saved:', data);
    //     }
    //   }
    }
  };
  </script>
  
  <style scoped>
  /* Add any specific styling here */
  </style>
  