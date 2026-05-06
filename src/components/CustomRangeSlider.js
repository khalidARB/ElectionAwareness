import { useState, useRef, useCallback } from '@wordpress/element';

const CustomRangeSlider = ({ label, help, value, onChange, min = 0, max = 100 }) => {
    const [inputValue, setInputValue] = useState(String(value));
    const trackRef = useRef(null);

    // Calculate percentage for fill
    const percentage = ((value - min) / (max - min)) * 100;

    const handleSliderChange = (e) => {
        const newValue = parseInt(e.target.value, 10);
        onChange(newValue);
        setInputValue(String(newValue));
    };

    const handleInputChange = (e) => {
        setInputValue(e.target.value);
    };

    const handleInputBlur = () => {
        let parsed = parseInt(inputValue, 10);
        if (isNaN(parsed)) parsed = min;
        if (parsed < min) parsed = min;
        if (parsed > max) parsed = max;
        onChange(parsed);
        setInputValue(String(parsed));
    };

    const handleInputKeyDown = (e) => {
        if (e.key === 'Enter') {
            handleInputBlur();
        }
    };

    // Keep input in sync when value changes externally
    if (String(value) !== inputValue && document.activeElement !== document.querySelector('.custom-range-input')) {
        // Only sync if this input is not focused
    }

    return (
        <div className="custom-range-slider">
            {label && <label className="custom-range-label">{label}</label>}
            <div className="custom-range-controls">
                <div className="custom-range-track-wrapper" ref={trackRef}>
                    <input
                        type="range"
                        className="custom-range-native"
                        min={min}
                        max={max}
                        value={value}
                        onChange={handleSliderChange}
                        style={{
                            background: `linear-gradient(to right, #FFFF00 0%, #FFFF00 ${percentage}%, rgba(255,255,255,0.1) ${percentage}%, rgba(255,255,255,0.1) 100%)`
                        }}
                    />
                </div>
                <input
                    type="number"
                    className="custom-range-input"
                    value={inputValue}
                    onChange={handleInputChange}
                    onBlur={handleInputBlur}
                    onKeyDown={handleInputKeyDown}
                    min={min}
                    max={max}
                />
            </div>
            {help && <p className="custom-range-help">{help}</p>}
        </div>
    );
};

export default CustomRangeSlider;
