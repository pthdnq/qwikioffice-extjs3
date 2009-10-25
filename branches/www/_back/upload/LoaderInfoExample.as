package {
    import flash.display.Loader;
    import flash.display.LoaderInfo;
    import flash.display.Sprite;
    import flash.events.*;
    import flash.net.URLRequest;

    public class LoaderInfoExample extends Sprite {
        private var url:String = "button1.jpg";
		private var url2:String = "button2.jpg";
		private var sprite1:Loader;
		private var sprite2:Loader;
        public function LoaderInfoExample() {
            sprite1 = Load(url);
			addChild(sprite1);
			sprite2 = Load(url2);
			sprite2.x = -800;
			addChild(sprite2);
			//trace(sprite2.width);
        }

        private function initHandler(event:Event):void {
            var loader:Loader = Loader(event.target.loader);
            var info:LoaderInfo = LoaderInfo(loader.contentLoaderInfo);
            //trace("initHandler: loaderURL=" + info.loaderURL + " url=" + info.url);
        }

        private function ioErrorHandler(event:IOErrorEvent):void {
            trace("ioErrorHandler: " + event);
        }
		
		private function Load(purl):Loader{
			var loader:Loader = new Loader();
            loader.contentLoaderInfo.addEventListener(Event.INIT, initHandler);
            loader.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);
            var request:URLRequest = new URLRequest(purl);
            loader.load(request);
		    return loader;
		}
		/**
		  * zmiana obrazka
		  */
		public function changeSprite(event:MouseEvent):void{
			//trace(event.type);
			if(event.type=='mouseOver'){
				sprite2.x = 0;
			}else{
				sprite2.x = -sprite2.width;
			}
		}
    }
}