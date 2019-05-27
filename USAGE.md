# Usage
- Make sure to install the New Relic PHP Agent first. See the New Relic site for details on this.
- Make sure the New Relic PHP Agent is running.
- Make sure that you are receiving data in your New Relic monitoring.
- Install our extension for Magento. 
- Configure the options in the Magento Configuration:
    Set the **Application Name** to whatever you like.
    Configure the **License Key** as received from New Relic.

# Technical overview
This extension implements an `Observer` to send various events to the New Relic dashboard. This extension implements two `Blocks` for browser timings via New Relic. One block for the header, one block for the body. 
This extension intercepts the class `Magento\Framework\View\Element\AbstractBlock` to send a custom parameter per Magento block to New Relic.
