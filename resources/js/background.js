chrome.runtime.onMessage.addListener((message, sender, sendResponse) => {
    if (message.action === "greet") {
      sendResponse({ response: "Hello from background script!" });
    }
    return true;
  });
  