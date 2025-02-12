<?php
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$appState = $objectManager->get('Magento\Framework\App\State');
$appState->setAreaCode('frontend');

$productRepository = $objectManager->get('Magento\Catalog\Api\ProductRepositoryInterface');
$mediaConfig = $objectManager->get('Magento\Catalog\Model\Product\Media\Config');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sku'])) {
    $sku = trim($_POST['sku']);
    
    try {
        $product = $productRepository->get($sku);
        $mediaGallery = $product->getMediaGalleryEntries();
        
        if (!$mediaGallery) {
            echo "<p>No images found for this product.</p>";
            exit;
        }
        
        $mediaBaseUrl = $mediaConfig->getBaseMediaUrl();
        $zip = new ZipArchive();
        $zipFileName = __DIR__ . "/product_images_{$sku}.zip";
        
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($mediaGallery as $image) {
                $imageUrl = $mediaBaseUrl . $image->getFile();
                $imageName = basename($image->getFile());
                $imageContent = file_get_contents($imageUrl);
                $zip->addFromString($imageName, $imageContent);
            }
            $zip->close();
            
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);
            unlink($zipFileName);
            exit;
        }
    } catch (Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Download Product Images</title>
</head>
<body>
    <form method="post">
        <label for="sku">Enter Product SKU:</label>
        <input type="text" id="sku" name="sku" required>
        <button type="submit">Download Images</button>
    </form>
</body>
</html>