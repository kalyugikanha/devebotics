import { initializeApp } from 'firebase/app';
import { getFirestore } from 'firebase/firestore';
import { getAuth } from 'firebase/auth';
import { getStorage } from 'firebase/storage';

const firebaseConfig = {
  apiKey: "AIzaSyAJHJmEkMNRJxIhoduTrbVV0KAdXLRBF2g",
  authDomain: "devebotics.firebaseapp.com",
  projectId: "devebotics",
  storageBucket: "devebotics.firebasestorage.app",
  messagingSenderId: "260521546441",
  appId: "1:260521546441:web:89489d1416433c91d23318",
  measurementId: "G-432PKM5EQ1"
};

const app = initializeApp(firebaseConfig);

export const db = getFirestore(app);
export const auth = getAuth(app);
export const storage = getStorage(app);
export default app;
