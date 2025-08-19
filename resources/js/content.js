chrome.runtime.sendMessage({ action: "greet" }, (response) => {
    if (chrome.runtime.lastError) {
      console.error(chrome.runtime.lastError.message);
    } else {
      console.log(response.response);
    }
  });
  