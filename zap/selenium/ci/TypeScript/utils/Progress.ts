const sleep = (msec: number) => new Promise(resolve => setTimeout(resolve, msec));
export const intervalRepeater = async (callback: any, interval: number) => {
  let progress = await callback();

  while (progress < 100) {
    progress = await callback();
    console.log(`Active Scan progress : ${progress}%`);
    await sleep(interval);
  }
}
