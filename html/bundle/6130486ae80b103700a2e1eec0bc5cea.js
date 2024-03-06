ace.define("ace/mode/doc_comment_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],(function(e,t,o){"use strict";var i=e("../lib/oop"),r=e("./text_highlight_rules").TextHighlightRules,n=function(){this.$rules={start:[{token:"comment.doc.tag",regex:"@\\w+(?=\\s|$)"},n.getTagRule(),{defaultToken:"comment.doc",caseInsensitive:!0}]}};i.inherits(n,r),n.getTagRule=function(e){return{token:"comment.doc.tag.storage.type",regex:"\\b(?:TODO|FIXME|XXX|HACK)\\b"}},n.getStartRule=function(e){return{token:"comment.doc",regex:"\\/\\*(?=\\*)",next:e}},n.getEndRule=function(e){return{token:"comment.doc",regex:"\\*\\/",next:e}},t.DocCommentHighlightRules=n})),ace.define("ace/mode/asl_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/doc_comment_highlight_rules","ace/mode/text_highlight_rules"],(function(e,t,o){"use strict";var i=e("../lib/oop"),r=e("./doc_comment_highlight_rules").DocCommentHighlightRules,n=e("./text_highlight_rules").TextHighlightRules,a=function(){var e=this.createKeywordMapper({keyword:"Default|DefinitionBlock|Device|Method|Else|ElseIf|For|Function|If|Include|Method|Return|Scope|Switch|Case|While|Break|BreakPoint|Continue|NoOp|Wait|True|False|AccessAs|Acquire|Alias|BankField|Buffer|Concatenate|ConcatenateResTemplate|CondRefOf|Connection|CopyObject|CreateBitField|CreateByteField|CreateDWordField|CreateField|CreateQWordField|CreateWordField|DataTableRegion|Debug|DMA|DWordIO|DWordMemory|DWordSpace|EisaId|EISAID|EndDependentFn|Event|ExtendedIO|ExtendedMemory|ExtendedSpace|External|Fatal|Field|FindSetLeftBit|FindSetRightBit|FixedDMA|FixedIO|Fprintf|FromBCD|GpioInt|GpioIo|I2CSerialBusV2|IndexField|Interrupt|IO|IRQ|IRQNoFlags|Load|LoadTable|Match|Memory32|Memory32Fixed|Mid|Mutex|Name|Notify|Offset|ObjectType|OperationRegion|Package|PowerResource|Printf|QWordIO|QWordMemory|QWordSpace|RawDataBuffer|Register|Release|Reset|ResourceTemplate|Signal|SizeOf|Sleep|SPISerialBusV2|Stall|StartDependentFn|StartDependentFnNoPri|Store|ThermalZone|Timer|ToBCD|ToBuffer|ToDecimalString|ToInteger|ToPLD|ToString|ToUUID|UARTSerialBusV2|Unicode|Unload|VendorLong|VendorShort|WordBusNumber|WordIO|WordSpace","constant.numeric":"One|Ones|Zero","keyword.operator":"Add|And|Decrement|Divide|Increment|Index|LAnd|LEqual|LGreater|LGreaterEqual|LLess|LLessEqual|LNot|LNotEqual|LOr|Mod|Multiply|NAnd|NOr|Not|Or|RefOf|Revision|ShiftLeft|ShiftRight|Subtract|XOr|DerefOf","constant.language":"__FILE__|__PATH__|__LINE__|__DATE__|__IASL__","storage.type":"UnknownObj|IntObj|StrObj|BuffObj|PkgObj|FieldUnitObj|DeviceObj|EventObj|MethodObj|MutexObj|OpRegionObj|PowerResObj|ProcessorObj|ThermalZoneObj|BuffFieldObj|DDBHandleObj","constant.library":"AttribQuick|AttribSendReceive|AttribByte|AttribBytes|AttribRawBytes|AttribRawProcessBytes|AttribWord|AttribBlock|AttribProcessCall|AttribBlockProcessCall|AnyAcc|ByteAcc|WordAcc|DWordAcc|QWordAcc|BufferAcc|AddressRangeMemory|AddressRangeReserved|AddressRangeNVS|AddressRangeACPI|RegionSpaceKeyword|FFixedHW|PCC|AddressingMode7Bit|AddressingMode10Bit|DataBitsFive|DataBitsSix|DataBitsSeven|DataBitsEight|DataBitsNine|BusMaster|NotBusMaster|ClockPhaseFirst|ClockPhaseSecond|ClockPolarityLow|ClockPolarityHigh|SubDecode|PosDecode|BigEndianing|LittleEndian|FlowControlNone|FlowControlXon|FlowControlHardware|Edge|Level|ActiveHigh|ActiveLow|ActiveBoth|Decode16|Decode10|IoRestrictionNone|IoRestrictionInputOnly|IoRestrictionOutputOnly|IoRestrictionNoneAndPreserve|Lock|NoLock|MTR|MEQ|MLE|MLT|MGE|MGT|MaxFixed|MaxNotFixed|Cacheable|WriteCombining|Prefetchable|NonCacheable|MinFixed|MinNotFixed|ParityTypeNone|ParityTypeSpace|ParityTypeMark|ParityTypeOdd|ParityTypeEven|PullDefault|PullUp|PullDown|PullNone|PolarityHigh|PolarityLow|ISAOnlyRanges|NonISAOnlyRanges|EntireRange|ReadWrite|ReadOnly|UserDefRegionSpace|SystemIO|SystemMemory|PCI_Config|EmbeddedControl|SMBus|SystemCMOS|PciBarTarget|IPMI|GeneralPurposeIO|GenericSerialBus|ResourceConsumer|ResourceProducer|Serialized|NotSerialized|Shared|Exclusive|SharedAndWake|ExclusiveAndWake|ControllerInitiated|DeviceInitiated|StopBitsZero|StopBitsOne|StopBitsOnePlusHalf|StopBitsTwo|Width8Bit|Width16Bit|Width32Bit|Width64Bit|Width128Bit|Width256Bit|SparseTranslation|DenseTranslation|TypeTranslation|TypeStatic|Preserve|WriteAsOnes|WriteAsZeros|Transfer8|Transfer16|Transfer8_16|ThreeWireMode|FourWireMode","invalid.deprecated":"Memory24|Processor"},"identifier");this.$rules={start:[{token:"comment",regex:"\\/\\/.*$"},r.getStartRule("doc-start"),{token:"comment",regex:"\\/\\*",next:"comment"},r.getStartRule("doc-start"),{token:"comment",regex:"\\[",next:"ignoredfield"},{token:"variable",regex:"\\Local[0-7]|\\Arg[0-6]"},{token:"keyword",regex:"#\\s*(?:define|elif|else|endif|error|if|ifdef|ifndef|include|includebuffer|line|pragma|undef|warning)\\b",next:"directive"},{token:"string",regex:'["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'},{token:"constant.character",regex:"['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"},{token:"constant.numeric",regex:/0[xX][0-9a-fA-F]+\b/},{token:"constant.numeric",regex:/[0-9]+\b/},{token:e,regex:"[a-zA-Z_$][a-zA-Z0-9_$]*\\b"},{token:"keyword.operator",regex:/[!\~\*\/%+-<>\^|=&]/},{token:"lparen",regex:"[[({]"},{token:"rparen",regex:"[\\])}]"},{token:"text",regex:"\\s+"}],comment:[{token:"comment",regex:"\\*\\/",next:"start"},{defaultToken:"comment"}],ignoredfield:[{token:"comment",regex:"\\]",next:"start"},{defaultToken:"comment"}],directive:[{token:"constant.other.multiline",regex:/\\/},{token:"constant.other.multiline",regex:/.*\\/},{token:"constant.other",regex:"\\s*<.+?>*s",next:"start"},{token:"constant.other",regex:'\\s*["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]*s',next:"start"},{token:"constant.other",regex:"\\s*['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']",next:"start"},{token:"constant.other",regex:/[^\\\/]+/,next:"start"}]},this.embedRules(r,"doc-",[r.getEndRule("start")])};i.inherits(a,n),t.ASLHighlightRules=a})),ace.define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],(function(e,t,o){"use strict";var i=e("../../lib/oop"),r=e("../../range").Range,n=e("./fold_mode").FoldMode,a=t.FoldMode=function(e){e&&(this.foldingStartMarker=new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/,"|"+e.start)),this.foldingStopMarker=new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/,"|"+e.end)))};i.inherits(a,n),function(){this.foldingStartMarker=/([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/,this.foldingStopMarker=/^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/,this.singleLineBlockCommentRe=/^\s*(\/\*).*\*\/\s*$/,this.tripleStarBlockCommentRe=/^\s*(\/\*\*\*).*\*\/\s*$/,this.startRegionRe=/^\s*(\/\*|\/\/)#?region\b/,this._getFoldWidgetBase=this.getFoldWidget,this.getFoldWidget=function(e,t,o){var i=e.getLine(o);if(this.singleLineBlockCommentRe.test(i)&&!this.startRegionRe.test(i)&&!this.tripleStarBlockCommentRe.test(i))return"";var r=this._getFoldWidgetBase(e,t,o);return!r&&this.startRegionRe.test(i)?"start":r},this.getFoldWidgetRange=function(e,t,o,i){var r,n=e.getLine(o);if(this.startRegionRe.test(n))return this.getCommentRegionBlock(e,n,o);if(r=n.match(this.foldingStartMarker)){var a=r.index;if(r[1])return this.openingBracketBlock(e,r[1],o,a);var s=e.getCommentFoldRange(o,a+r[0].length,1);return s&&!s.isMultiLine()&&(i?s=this.getSectionRange(e,o):"all"!=t&&(s=null)),s}return"markbegin"!==t&&(r=n.match(this.foldingStopMarker))?(a=r.index+r[0].length,r[1]?this.closingBracketBlock(e,r[1],o,a):e.getCommentFoldRange(o,a,-1)):void 0},this.getSectionRange=function(e,t){for(var o=e.getLine(t),i=o.search(/\S/),n=t,a=o.length,s=t+=1,l=e.getLength();++t<l;){var d=(o=e.getLine(t)).search(/\S/);if(-1!==d){if(i>d)break;var c=this.getFoldWidgetRange(e,"all",t);if(c){if(c.start.row<=n)break;if(c.isMultiLine())t=c.end.row;else if(i==d)break}s=t}}return new r(n,a,s,e.getLine(s).length)},this.getCommentRegionBlock=function(e,t,o){for(var i=t.search(/\s*$/),n=e.getLength(),a=o,s=/^\s*(?:\/\*|\/\/|--)#?(end)?region\b/,l=1;++o<n;){t=e.getLine(o);var d=s.exec(t);if(d&&(d[1]?l--:l++,!l))break}if(o>a)return new r(a,i,o,t.length)}}.call(a.prototype)})),ace.define("ace/mode/asl",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/asl_highlight_rules","ace/mode/folding/cstyle"],(function(e,t,o){"use strict";var i=e("../lib/oop"),r=e("./text").Mode,n=e("./asl_highlight_rules").ASLHighlightRules,a=e("./folding/cstyle").FoldMode,s=function(){this.HighlightRules=n,this.foldingRules=new a,this.$behaviour=this.$defaultBehaviour};i.inherits(s,r),function(){this.$id="ace/mode/asl"}.call(s.prototype),t.Mode=s})),ace.require(["ace/mode/asl"],(function(e){"object"==typeof module&&"object"==typeof exports&&module&&(module.exports=e)}));