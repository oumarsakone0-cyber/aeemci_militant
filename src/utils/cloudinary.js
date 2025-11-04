const cloudinaryConfig = {
  cloudName: "r-sidence-meubl-e",
  uploadPreset: "dsegjq9c",
  apiKey: "162815926914253",
  uploadUrl: "https://api.cloudinary.com/v1_1/r-sidence-meubl-e/upload",
}

export const uploadToCloudinary = async (file, onProgress = null) => {
  try {
    const fileName = `aeemci_${file.type.includes("image") ? "photo" : "document"}_${Date.now()}_${file.name.replace(/\s+/g, "_")}`

    const uploadFormData = new FormData()
    uploadFormData.append("file", file)
    uploadFormData.append("upload_preset", cloudinaryConfig.uploadPreset)
    uploadFormData.append("api_key", cloudinaryConfig.apiKey)
    uploadFormData.append("public_id", fileName)

    const response = await fetch(cloudinaryConfig.uploadUrl, {
      method: "POST",
      body: uploadFormData,
    })

    if (!response.ok) {
      throw new Error("Upload failed")
    }

    const data = await response.json()

    if (data && data.secure_url) {
      return {
        success: true,
        url: data.secure_url,
        publicId: data.public_id,
      }
    } else {
      throw new Error("Invalid Cloudinary response")
    }
  } catch (error) {
    console.error("Error uploading to Cloudinary:", error)
    return {
      success: false,
      error: "Erreur lors du téléchargement. Veuillez réessayer.",
    }
  }
}
