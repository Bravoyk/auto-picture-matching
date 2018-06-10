package main

import (
	"flag"
	"fmt"

	"github.com/go-vgo/robotgo"
)

// CaptureScreen 截取全屏, path 是保存的路径
func CaptureScreen(path string) {
	bitmap := robotgo.CaptureScreen()

	robotgo.SaveBitmap(bitmap, path)
}

// MouseClick 模拟鼠标左键点击, x, y 为坐标
func MouseClick(x int, y int) {
	robotgo.MoveClick(x, y, "left")
}

func main() {
	handle := flag.String("handle", "", "expect capture, click.")

	path := flag.String("path", "", "image save path.")

	cx := flag.Int("cx", -1, "mouse click position x.")

	cy := flag.Int("cy", -1, "mouse click position y.")

	flag.Parse()

	if *handle == "" {
		fmt.Println("Please input handle!")
		return
	}

	if *handle == "capture" {
		if *path == "" {
			fmt.Println("Please input image save path!")
		} else {
			CaptureScreen(*path)
		}
	}

	if *handle == "click" {
		if *cx > 0 && *cy > 0 {
			MouseClick(*cx, *cy)
		} else {
			fmt.Println("Mouse postion invalid.")
		}
	}
}
