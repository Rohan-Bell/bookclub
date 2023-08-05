// Function to generate a random Data Encryption Key (DEK)
async function generateDEK() {
    const key = await crypto.subtle.generateKey(
      {
        name: "AES-GCM",
        length: 256,
      },
      true, // Whether the key is extractable (i.e., can be used for encryption/decryption)
      ["encrypt", "decrypt"] // Key usages
    );
    return key;
  }
  
  // Function to encrypt data with AES-256
  async function encryptData(data, key) {
    const encodedData = new TextEncoder().encode(data);
    const iv = crypto.getRandomValues(new Uint8Array(12)); // Initialization Vector (IV)
    const encryptedData = await crypto.subtle.encrypt(
      {
        name: "AES-GCM",
        iv,
      },
      key,
      encodedData
    );
    return {
      iv: iv,
      data: new Uint8Array(encryptedData),
    };
  }
  
  // Function to decrypt data with AES-256
  async function decryptData(encryptedData, key) {
    const decryptedData = await crypto.subtle.decrypt(
      {
        name: "AES-GCM",
        iv: encryptedData.iv,
      },
      key,
      encryptedData.data
    );
    return new TextDecoder().decode(decryptedData);
  }
  
  // Example usage:
  async function example() {
    try {
      const originalData = "Hello, this is a secret message!";
      const dek = await generateDEK();
  
      // Encrypt the data
      const encryptedData = await encryptData(originalData, dek);
  
      // ... Store or transmit the encryptedData.iv and encryptedData.data ...
  
      // Decrypt the data
      const decryptedData = await decryptData(encryptedData, dek);
      console.log(decryptedData); // Output: "Hello, this is a secret message!"
    } catch (error) {
      console.error(error);
    }
  }
  
  example();
  
  
  
  
  
  
  